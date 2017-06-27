<?php
namespace ORG;

class Contact //extends AnotherClass
{
	public $count;
	public $ID;
	public $title;

	function __construct($count=0){
		$this->count = is_int($count) ? $count : false;

		$this->ID = $this->_contact_post();
	}

	public function _contact_post(){
		$contact_ids = get_option( 'contact_ids', false );

		if( isset($contact_ids[$this->count]) ){
			$_post = get_post($contact_ids[$this->count]);

			$this->title = $_post->post_title;
			return $_post->ID;
		}

		return false;
	}
}

$contact = new Contact();
var_dump(\WPForm::active(ORG_METANAME, false, true, $contact->ID));
 // var_dump( get_post_meta( $contact->ID, 'contacts', true ) );
// var_dump($contact->_set_main_data());
// add_filter( 'rq_title', 'ORG\title_builtin', 10, 2 );
// function title_builtin($name, $link){
// 	$tag = 'h4';
// 	if( $name )
// 		$name = "<{$tag}>{$name}</{$tag}>";
	
// 	if( $link )
// 		$name = "<a href='{$link}'>{$name}</a>";

// 	return $name;
// }

/**
 * Show Review Post From Archive 
 */
// add_shortcode( 'RQ_ARCHIVE', 'RQ\posts_render' );
// add_shortcode( 'RQ_POSTS', 'RQ\posts_render' );
// add_shortcode( 'rq', 'RQ\posts_render' );
// function posts_render(){
// 	$query = new \WP_Query(	array(
// 		'post_type' => RQ_TYPE,
// 		'posts_per_page' => -1,
// 		'post_status' => 'publish',
// 		//'order'   => 'DESC', // or ASC
// 		) );

// 	ob_start();
// 	while ( $query->have_posts() ) {
// 		$query->the_post();

// 		get_template_part( 'template-parts/content', RQ_TYPE );
// 	}
// 	return ob_get_clean();
// }

/**
 * Show Review Forms
 */
if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly
	/*
	$query = new \WP_Query(	array(
		'post_type' => ORG_SLUG,
		'posts_per_page' => -1,
		'post_status' => 'publish',
		//'order'   => 'DESC', // or ASC
		) );

	// ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();

		var_dump(get_the_title() );
		the_title();
		//get_template_part( 'template-parts/content', ORG_SLUG );
	}
	return ob_get_clean();
*/

		/*
// [our_address], [our_numbers], [our_email], [our_time_work], [our_socials] - for easy use
function get_company_info( $field, $filter = 'the_content' ){
  $info = get_theme_mod( 'company_' . $field );
  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
  elseif( $filter )
    return apply_filters( $filter, $info );

  return $info;
}
function get_company_address(){ return get_company_info('address'); }
function get_company_numbers(){ return get_company_info('numbers'); }
function get_company_time_work(){ return get_company_info('time_work'); }
function get_company_email(){ return get_company_info('email'); }
function get_company_socials(){ return get_company_info('socials'); }
function get_company_first_number( $del = ',', $num=0, $filter = 'the_content' ) {
  // for shortcode
  if(! $del) $del = ',';
  if(! $num) $num = 0;
  if(! $filter || $filter != false) $filter = 'the_content';

  $numbers = get_company_info('numbers', false);
  $info = explode($del, $numbers);
  
  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info[$num] )) );
  elseif( $filter )
    return apply_filters( $filter, $info[$num] );

  return $info[$num];
}
add_shortcode('our_address', 'get_company_address');
add_shortcode('our_numbers', 'get_company_numbers');
add_shortcode('our_first_number', 'get_company_first_number');
add_shortcode('our_time_work', 'get_company_time_work');
add_shortcode('our_email', 'get_company_email');
add_shortcode('our_socials', 'get_company_socials');

function company_display_settings($wp_customize){
  $wp_customize->add_section('company_options', array(
    'title'     => 'Информация о компании',
    'priority'  => 60,
    'description' => 'Добавьте информации о своей организации'
    )
  );

  $wp_customize->add_setting('company_address');
  $wp_customize->add_control('company_address',
    array(
      'type'     => 'textarea',
      'label'    => 'Адрес',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_numbers');
  $wp_customize->add_control('company_numbers',
    array(
      'type'     => 'textarea',
      'label'    => 'Номера телефонов',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_email');
  $wp_customize->add_control('company_email',
    array(
      'type'     => 'text',
      'label'    => 'Email адрес',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_time_work');
  $wp_customize->add_control('company_time_work',
    array(
      'type'     => 'textarea',
      'label'    => 'Режим работы',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_socials');
  $wp_customize->add_control('company_socials',
    array(
      'type'     => 'textarea',
      'label'    => 'Социальные сети',
      'section'  => 'company_options',
      )
    );
}
add_action( 'customize_register', 'company_display_settings' );*/