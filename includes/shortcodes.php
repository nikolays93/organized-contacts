<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

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
        'del'    => ', ',
        'filter' => 'the_content',
        'before' => '',
        'after'  => '',
    ), $atts );

    if( 'phone' == $atts['field'] )
        return get_company_number($atts);

    if( 'phones' == $atts['field'] )
        $atts['field'] = 'numbers';

    $atts['fields'] = array_map('trim', explode(',', $atts['field']));

    $info = array();
    foreach ($atts['fields'] as $field) {
        $atts['field'] = $field;
        $atts['filter'] = false;

        $info[] = apply_filters( 'company_info_field_filter',
            company_info($field, $atts['id']), $atts );
    }

    $info = apply_filters( 'company_info_summary_filter',
        implode($atts['del'], $info), $atts );

    return $info;
}


function get_company_number( $atts = false ) {
    $atts = shortcode_atts( array(
        'id'     => 'primary',
        'filter' => 'the_content',
        'del'    => ',',
        'num'    => '1',
        'before' => '',
        'after'  => '',
    ), $atts);

    $info = explode($atts['del'], company_info( 'numbers', $atts['id'] ));
    return apply_filters( 'company_info_shortcode_filter', $info[ $atts['num'] - 1 ], $atts );
}
