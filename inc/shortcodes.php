<?php

function get_company_info( $field, $filter = 'the_content' ){
  global $post;

  if( empty(ORGContacts::$settings['advanced_contacts']) ){
    $info = get_theme_mod( 'company_' . $field );
  }
  else {
    $ids = get_option( ORGContacts::ORGS_ID, array() );
    if( isset($post->ID) && array_key_exists($post->ID, $ids) ){
      $contact_id = $post->ID;
    }
    else {
      $args = array(
        'post__in'   => array_keys($ids),
        'post_type'  => ORGContacts::SLUG,
        'meta_query' => array(
          array(
            'key'     => '_primary',
            'value'   => 'on',
            )
          )
        );
      $query = new WP_Query( $args );

      if ( $query->have_posts() ) {
        $query->the_post();
        $contact_id = get_the_id();
      }
      else {
        reset($ids);
        $contact_id = key($ids);
      }
      wp_reset_postdata();
    }

    if( !$contact_id )
      return false;

    $all_data = get_post_meta( $contact_id, '_'.ORGContacts::SLUG, true );
    $info = isset($all_data[$field]) ? $all_data[$field] : ' ';
  }

  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
  elseif( $filter )
    return apply_filters( $filter, $info );

  return $info;
}

function get_company_address($atts){
  extract( shortcode_atts( array('filter' => 'the_content'), $atts) );
  return get_company_info('address', $filter);
}
function get_company_numbers($atts){
  extract( shortcode_atts( array('filter' => 'the_content'), $atts) );
  return get_company_info('numbers', $filter);
}
function get_company_time_work($atts){
  extract( shortcode_atts( array('filter' => 'the_content'), $atts) );
  return get_company_info('time_work', $filter);
}
function get_company_email($atts){
  extract( shortcode_atts( array('filter' => 'the_content'), $atts) );
  return get_company_info('email', $filter);
}
function get_company_socials($atts){
  extract( shortcode_atts( array('filter' => 'the_content'), $atts) );
  return get_company_info('socials', $filter);
}
function get_company_number( $atts=false, $content=false, $shortcode='get_company_number', $filter = false ) {
  $atts = shortcode_atts(array(
    'filter' => 'the_content',
    'del' => ',',
    'num' => '0',
    ), $atts);

  if( $filter )
    $atts['filter'] = $filter;

  $numbers = get_company_info('numbers', false);
  $info = explode($atts['del'], $numbers);

  if($atts['filter'] == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info[$atts['num']] )) );
  elseif( $atts['filter'] )
    return apply_filters( $atts['filter'], $info[$atts['num']] );

  return $info[$atts['num']];
}
// add_shortcode('our_address', 'get_company_address');
// add_shortcode('our_numbers', 'get_company_numbers');
// add_shortcode('our_first_number', 'get_company_number');
// add_shortcode('our_time_work', 'get_company_time_work');
// add_shortcode('our_email', 'get_company_email');
// add_shortcode('our_socials', 'get_company_socials');
