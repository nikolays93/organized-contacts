<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

function company_concat_sides($info, $atts = array()) {
    if( !$info ) return $info;

    $before = !empty( $atts['before'] ) ? $atts['before'] : '';
    $after =  !empty( $atts['after'] ) ? $atts['after'] : '';

    $info = $before . $info . $after;

    return $info;
}

function company_format_filter($info, $atts = array()) {
    if( !$info ) return $info;

    $filter = !empty( $atts['filter'] ) ? $atts['filter'] : null;

    if( 'the_content' == $filter ) {
        $info = str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
    }
    // filter exists and not disable
    elseif($filter && !in_array($filter, array('false', false, 'none', 'disable', '0', 'off'))) {
        $info = apply_filters( $filter, $info );
    }

    return $info;
}

function schema_format_summary_filter($info, $atts = array()) {
    if( in_array('city', $atts['fields']) || in_array('address', $atts['fields']) ) {
        $info = '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' .$info. '</span>';
    }

    return $info;
}

function schema_format_filter($info, $atts = array()) {
    if( !$info || !isset($atts['field']) || !get_theme_mod( 'use_schema', false ) )
        return $info;

    switch ($atts['field']) {
        case 'name':
            $info = '<span itemprop="name">' .$info. '</span>';
            break;

        /**
         * @todo what u think about it?
         * <span itemprop="postalCode">119021</span>
         */
        case 'city':
            $info = '<span itemprop="addressLocality">' .$info. '</span>';
            break;

        case 'address':
            $info = '<span itemprop="streetAddress">' .$info. '</span>';
            break;

        case 'numbers':
            $info = '<span itemprop="telephone">' .$info. '</span>';
            break;

        case 'email':
            $info = '<span itemprop="email">' .$info. '</span>';
            break;

        case 'time_work':
            /**
             * @todo after set datepicker
             * datetime="Tu,Th 16:00−20:00"
             */
            $info = '<time itemprop="openingHours">' .$info. '</time>';
            break;

        case 'socials':
            break;
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
