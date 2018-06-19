<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

function absolute_to_relative( $value ) {
    $value = esc_url( $value );
    $value = str_replace(get_site_url(), '', $value);

    return $value;
}

function relative_to_absolute( $value ) {
    if( is_string($value) )
        $value = get_site_url() . $value;

    return $value;
}

function add_company_fields(&$wp_customize, $company_id, $section) {
    $wp_customize->add_setting($company_id . '_company_name', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_name', array(
        'type'     => 'text',
        'label'    => __('Your company name', DOMAIN), //'Название организации',
        'section'  => $section,
        'priority' => 10,
    ) );

    $wp_customize->add_setting($company_id . '_company_image', array(
        'sanitize_callback' => __NAMESPACE__ . '\absolute_to_relative',
        'transport' => 'postMessage'
    ));

    $wp_customize->add_control(
        new \WP_Customize_Image_Control(
            $wp_customize,
            $company_id . '_company_image',
            array(
                'label'      => __('Your company image', DOMAIN),
                'section'    => $section,
                'priority'   => 20,
                'settings'   => $company_id . '_company_image',
            )
        )
    );

    $wp_customize->add_setting($company_id . '_company_address', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_address', array(
        'type'     => 'textarea',
        'label'    => __('Your company address', DOMAIN), //'Адрес',
        'priority' => 30,
        'section'  => $section,
        ) );

    $wp_customize->add_setting($company_id . '_company_numbers', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_numbers', array(
        'type'     => 'textarea',
        'label'    => __('Phone numbers', DOMAIN), //'Номера телефонов',
        'priority' => 40,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_email', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_email', array(
        'type'     => 'text',
        'label'    => __('Email address', DOMAIN), // 'Email адрес',
        'priority' => 50,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_time_work', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_time_work', array(
        'type'     => 'textarea',
        'label'    => __('Work time mode', DOMAIN), // 'Режим работы',
        'priority' => 60,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_socials', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_socials', array(
        'type'     => 'textarea',
        'label'    => __('Social links', DOMAIN), // 'Социальные сети',
        'priority' => 70,
        'section'  => $section,
        ) );

    do_action( 'add_company_custom_fields', $wp_customize, $section, $company_id );
}


function customizer($wp_customize) {
    $organizations = get_companies();
    $count = sizeof( $organizations );
    $parent_menu_args = array(
        'priority'       => 40,
        'capability'     => 'edit_theme_options',
        'title'          => __('Contacts', DOMAIN),
        'description'    => __('Add you company\'s contacts', DOMAIN), // 'Добавьте информации о своей организации',
    );
    $counter_args = array(
        'type'        => 'number',
        'label'       => __('Number of companies', DOMAIN),//'Название организации',
        'description' => __('Changes will be applied after the page refresh', DOMAIN),
        'priority'    => '777',
        'section'     => 'primary_contact',
        'input_attrs' => array(
            'min' => 1,
            'max' => 99,
        ),
    );

    $panel = '';
    if( 1 >= $count ) {
        $wp_customize->add_section( 'primary_contact', $parent_menu_args );
    }
    else {
        $parent_menu_args['priority'] = 60;
        $counter_args['section'] = 'contacts_settings';
        $panel = 'Contacts';
        $wp_customize->add_panel( $panel, $parent_menu_args );

        $wp_customize->add_section( $counter_args['section'], array(
            'priority'       => 30,
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
            'title'          => __('Configuration', DOMAIN),
            'description'    => __('Set a general settings', DOMAIN),
            'panel'  => $panel,
        ) );
    }

    $wp_customize->add_setting('companies_count', array(
        'default' => 1,
        'transport' => 'postMessage'
        ));
    $wp_customize->add_control('companies_count', $counter_args);

    foreach ($organizations as $company_id => $company) {
        add_filter('theme_mod_' . $company_id . '_company_image', __NAMESPACE__ . '\relative_to_absolute');
        if( 1 < $count ) {
            $wp_customize->add_section( $company_id . '_contact', array(
                'priority'       => 10,
                'capability'     => 'edit_theme_options',
                'title'          => __($company),
                'description'    =>  __('Add you company\'s contacts', DOMAIN), // 'Добавьте информации о своей организации',
                'panel'  => $panel,
            ) );
        }

        add_company_fields( $wp_customize, $company_id, $company_id . '_contact' );
    }
}
