<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

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
