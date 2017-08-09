<?php

function company_format($info, $filter, $before = '', $after = ''){
  $info = $before . $info . $after;
  // default filter
  if($filter == 'the_content')
    $info = str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
  // filter exists and not disable
  elseif( $filter && !in_array($filter, array('false', false, 'none', 'disable', '0', 'off')) )
    $info = apply_filters( $filter, $info );

  return $info;
}

function company_info( $field ){
  global $post;

  if( $details = get_theme_mod( 'company_details', false ) && CONTACTS_SLUG == get_post_type( $post ) ){
    $all_data = get_post_meta( $post->ID, '_'.CONTACTS_SLUG, true );
    return isset($all_data[ $field ]) ? $all_data[ $field ] : '';
  }

  return get_theme_mod( 'company_' . $field, '' );
}

function company_info_shortcode($atts){
  extract( shortcode_atts( array(
    'field' => 'name',
    'filter' => 'the_content',
    'before' => '',
    'after' => ''),
  $atts) );

  if( $field == 'phone' )
    get_company_number($atts);

  elseif( $field == 'phones' )
   $field = 'numbers';

  return company_format(company_info($field), $filter, $before, $after);
}

function get_company_number( $atts=false, $content=false, $shortcode='our_first_number' ) {
  $atts = shortcode_atts(array(
    'filter' => 'the_content',
    'del' => ',',
    'num' => '1',
    'before' => '',
    'after' => '',
    ), $atts);

  $info = explode($atts['del'], company_info( 'numbers' ));
  $fkey = $atts['num'] - 1;
  return company_format($info[ $fkey ], $atts['filter'], $atts['before'], $atts['after']);
}

add_shortcode('company', 'company_info_shortcode');
add_shortcode('phone', 'get_company_number');
