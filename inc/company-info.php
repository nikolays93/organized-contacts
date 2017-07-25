<?php
if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly

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
add_action( 'customize_register', 'company_display_settings' );