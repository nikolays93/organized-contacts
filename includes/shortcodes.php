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
