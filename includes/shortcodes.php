<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

function company_format_filter($info, $filter = null, $before = '', $after = '') {
    if( ! $info  ) {
        return '';
    }

    $info = $before . $info . $after;

    if( $filter == 'the_content' ) {
        $info = str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
    }
    // filter exists and not disable
    elseif($filter && ! in_array($filter, array('false', false, 'none', 'disable', '0', 'off'))) {
        $info = apply_filters( $filter, $info );
    }

    return $info;
}

/**
 * @param  string $field      name | address | numbers | email | time_work | socials
 * @param  string $company_id primary | secondary | tertiary | quaternary | fivefold
 *
 * @return string
 */
function company_info( $field, $company_id = 'primary' ){
    if( 'image' == $field ) {
        if( $src = get_theme_mod( $company_id . '_company_' . $field, '' ) ) {
            return sprintf('<img src="%s" alt="" />', esc_attr( $src ) );
        }

        return '';
    }
    return get_theme_mod( $company_id . '_company_' . $field, '' );
}

function company_info_shortcode($atts){
    $atts = shortcode_atts( array(
        'id'     => 'primary',
        'field'  => 'name',
        'filter' => 'the_content',
        'before' => '',
        'after'  => ''
    ), $atts );

    if( 'phone' == $atts['field'] )
        return get_company_number($atts);

    if( 'phones' == $atts['field'] )
        $atts['field'] = 'numbers';

    return company_format_filter( company_info($atts['field'],
        $atts['id']), $atts['filter'], $atts['before'], $atts['after'] );
}

function get_company_number( $atts = false ) {
    $atts = shortcode_atts(array(
        'id'     => 'primary',
        'filter' => 'the_content',
        'del'    => ',',
        'num'    => '1',
        'before' => '',
        'after'  => '',
    ), $atts);

    $info = explode($atts['del'], company_info( 'numbers', $atts['id'] ));
    return company_format_filter($info[ $atts['num'] - 1 ],
        $atts['filter'], $atts['before'], $atts['after']);
}

add_shortcode('company', __NAMESPACE__ . '\company_info_shortcode');
add_shortcode('phone', __NAMESPACE__ . '\get_company_number');

// if ( user_can_richedit() ) {
    add_filter("mce_external_plugins", __NAMESPACE__ . '\mce_plugin');
    add_filter("mce_buttons", __NAMESPACE__ . '\mce_button');
    add_action("admin_head", __NAMESPACE__ . '\mce_enqueue');
// }

/** Register Shortcode Button MCE */
function mce_plugin($plugin_array) {
    $plugin_array['company'] = plugins_url( '../assets/company_button.js', __FILE__ );

    return $plugin_array;
}

function mce_button($buttons) {
    $buttons[] = 'company';

    return $buttons;
}

function mce_enqueue() {
    wp_enqueue_style( 'company', plugins_url( '../assets/company-icon.css', __FILE__ ) );

    wp_enqueue_script( 'company_shortcode', plugins_url( '../assets/company_shortcode.js', __FILE__ ),
        array( 'shortcode', 'wp-util', 'jquery' ), false, true );

    $organizations = array();
    if( 1 < $count = get_theme_mod('companies_count', 1) ) {
        $organizations = array(
            'primary'    => get_theme_mod( 'primary_company_name', 'Primary' ),
            'secondary'  => get_theme_mod( 'secondary_company_name', 'Secondary'),
            'tertiary'   => get_theme_mod( 'tertiary_company_name', 'Tertiary'),
            'quaternary' => get_theme_mod( 'quaternary_company_name', 'Quaternary'),
            'fivefold'   => get_theme_mod( 'fivefold_company_name', 'Fivefold'),
        );

        if( 1 <= $count - 5 ) {
            for ($i=6; $i <= $count; $i++) {
                $company_id = "company_{$i}";
                $company_name = ucfirst($company_id);

                $organizations[ $company_id ] = get_theme_mod( $company_id . '_company_name', $company_name );
                if( $company_name !== $organizations[ $company_id ]  ) {
                    $organizations[ $company_id ] .= " ($i)";
                }
            }
        }
        else {
            $organizations = array_slice($organizations, 0, $count);
        }
    }

    $companies = array();
    foreach ($organizations as $company_id => $company) {
        $companies[] = (object) array('text' => $company, 'value' => $company_id);
    }

    $arrFields = apply_filters( 'organized_contacts_mce_fields', array(
        'name' => __('Company name', DOMAIN),
        'image' => __('Company iamge', DOMAIN),
        'address' => __('Address', DOMAIN),
        'numbers' => __('Phone numbers', DOMAIN),
        'email' => __('Email', DOMAIN),
        'time_work' => __('Time work', DOMAIN),
        'socials' => __('Socials', DOMAIN),
    ) );

    $fields = array();
    foreach ($arrFields as $value => $text) {
        $fields[] = (object) array('text' => $text, 'value' => $value);
    }

    wp_localize_script( 'company_shortcode', 'company', array(
        'organizations' => $companies,
        'fields' => $fields,
    ) );

    wp_localize_script( 'company_shortcode', 'company_localize', array(
        'addButton_title' => __('Insert contact\'s company shortcode', DOMAIN),
        'about_organization' => __('About organization', DOMAIN), // 'Информация о компании',
        'insert' => __('Insert', DOMAIN),
        'filter' => __('Filter', DOMAIN),
        'howdisablefilter' => __('Use none for disable default the_content', DOMAIN),
        'htmlbefore' => __('HTML before', DOMAIN),
        'htmlafter' => __('HTML after', DOMAIN),
        'organization' => __('Organization', DOMAIN),
    ) );
}
