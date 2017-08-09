<?php
namespace Contacts;

if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly

function has_items( &$query ){
  if( !get_theme_mod( 'company_details', false ) )
    return false;

  $query = new \WP_Query( array(
    'post_type' => CONTACTS_SLUG,
    'posts_per_page' => -1,
    ) );
  return $query->have_posts();
}

function customizer_settings($wp_customize){
  $wp_customize->add_section('company_options', array(
    'title'     => 'Информация о компании',
    'priority'  => 60,
    'description' => 'Добавьте информации о своей организации'
    )
  );

  $wp_customize->add_setting('company_name');
  $wp_customize->add_control('company_name',
    array(
      'type'     => 'text',
      'label'    => 'Название организации',
      'section'  => 'company_options',
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

  $wp_customize->add_setting('company_details');
  $wp_customize->add_control('company_details',
    array(
      'type'     => 'checkbox',
      'label'    => 'Использовать расширенную информацию',
      'description' => 'Создает отдельный тип данных "Контакты", который можно изменить из административной части',
      'section'  => 'company_options',
      )
    );

  $query = '';
  if( has_items($query) ){
    $items = array();
    while ( $query->have_posts() ) { $query->the_post();
      $id = get_the_id();
      $items[$id] = get_the_title();
    }
    wp_reset_postdata();

    $wp_customize->add_setting('company_primary_id');
    $wp_customize->add_control('company_primary_id',
      array(
        'type'     => 'select',
        'label'    => 'Использовать как главные контакты',
        'section'  => 'company_options',
        'description' => 'В случае если у вас несколько организаций/адресов, установите одну из них главной',
        'choices'  => $items,
        )
      );
  }
}
add_action( 'customize_register', 'Contacts\customizer_settings' );

function update_primary_company(){
  $_post = array(
    'post_author'    => 1,
    'post_content'   => '',
    'post_excerpt'   => '',
    'post_status'      => 'publish',
    //'post_name'      => 'first_contact',
    'post_title'     => get_theme_mod( 'company_name' ),
    'post_type'      => CONTACTS_SLUG,
    'meta_input' => array(
      '_' . CONTACTS_SLUG => array(
        'address'   => get_theme_mod( 'company_address' ),
        'numbers'   => get_theme_mod( 'company_numbers' ),
        'email'     => get_theme_mod( 'company_email' ),
        'work-time' => get_theme_mod( 'company_time_work' ),
        'socials'   => get_theme_mod( 'company_socials' ),
        ),
      ),
    );

  /* Create First Organization if not exists or update if initialize */
  if( get_theme_mod( 'company_details' ) ){
    $query = '';
    if( ! has_items($query) ){
      $post_id = wp_insert_post( $_post, true );
      if( !is_wp_error($post_id) && $post_id)
        set_theme_mod( 'company_primary_id', $post_id );
    }
    else {
      if( $_post['ID'] = get_theme_mod( 'company_primary_id', false ) )
        wp_update_post( $_post );
    }
  }
}
add_action( 'customize_save_after', 'Contacts\update_primary_company' );

function enqueue_customizer_script(){
  wp_enqueue_script('company_customize', plugins_url( false, __FILE__ ) . '/customizer.js' , array('jquery'), '1', true);
  wp_localize_script( 'company_customize', 'contacts_customize', array('nonce' => wp_create_nonce( 'contacts' )) );
}
add_action( 'customize_controls_enqueue_scripts', 'Contacts\enqueue_customizer_script' );

function ajax_contact_postdata() {
  if( ! wp_verify_nonce( $_POST['nonce'], 'contacts' ) ){
    echo 'Ошибка! нарушены правила безопасности';
    wp_die();
  }

  if($post_id = intval( $_POST['contact_id'] )){
    $contacts = get_post_meta( $post_id, '_' . CONTACTS_SLUG, true );
    $contacts['name'] = get_the_title( $post_id );
    echo json_encode( $contacts );
  }
  else {
    echo 0;
  }

  wp_die();
}
add_action('wp_ajax_get_company_metas', 'Contacts\ajax_contact_postdata');