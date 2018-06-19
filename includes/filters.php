<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

function company_image_home_url($info, $atts = array()) {
    if( 'image' == $atts['field'] && !is_front_page() && 'primary' == $atts['id'] ) {
        $info = '<a href="' .home_url(). '">' .$info. '</a>';
    }

    return $info;
}

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