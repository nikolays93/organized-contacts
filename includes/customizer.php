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

function add_company_fields(&$wp_customize, $company_id, $personal_section = false) {
    $section = $personal_section ? 'company_contacts' : $company_id . '_contact';

    $wp_customize->add_setting($company_id . '_company_name');
    $wp_customize->add_control($company_id . '_company_name', array(
        'type'     => 'text',
        'label'    => __('Your company name', DOMAIN), //'Название организации',
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_image', array(
        'sanitize_callback' => __NAMESPACE__ . '\absolute_to_relative',
    ));

    $wp_customize->add_control(
       new \WP_Customize_Image_Control(
         $wp_customize,
         $company_id . '_company_image',
         array(
             'label'      => __('Your company image', DOMAIN),
             'section'    => $section,
             'settings'   => $company_id . '_company_image',
             )
         )
     );

    $wp_customize->add_setting($company_id . '_company_address');
    $wp_customize->add_control($company_id . '_company_address', array(
        'type'     => 'textarea',
        'label'    => __('Your company address', DOMAIN), //'Адрес',
        'section'  => $section,
        ) );

    $wp_customize->add_setting($company_id . '_company_numbers');
    $wp_customize->add_control($company_id . '_company_numbers', array(
        'type'     => 'textarea',
        'label'    => __('Phone numbers', DOMAIN), //'Номера телефонов',
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_email');
    $wp_customize->add_control($company_id . '_company_email', array(
        'type'     => 'text',
        'label'    => __('Email address', DOMAIN), // 'Email адрес',
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_time_work');
    $wp_customize->add_control($company_id . '_company_time_work', array(
        'type'     => 'textarea',
        'label'    => __('Work time mode', DOMAIN), // 'Режим работы',
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_socials');
    $wp_customize->add_control($company_id . '_company_socials', array(
        'type'     => 'textarea',
        'label'    => __('Social links', DOMAIN), // 'Социальные сети',
        'section'  => $section,
        ) );
}

add_action( 'customize_register', __NAMESPACE__ . '\customizer' );
function customizer($wp_customize) {
    if( 1 >= $count = get_theme_mod('companies_count', 1) ) {
        $wp_customize->add_section( 'company_contacts', array(
            'priority'       => 40,
            'capability'     => 'edit_theme_options',
            'title'          => __('Contacts', DOMAIN),
            'description'    => __('Add you company\'s contacts', DOMAIN), // 'Добавьте информации о своей организации',
        ) );

        add_company_fields( $wp_customize, 'primary', true );

        $section = 'company_contacts';
    }
    else {
        $organizations = array(
            'primary'    => get_theme_mod( 'primary_company_name', 'Primary' ),
            'secondary'  => get_theme_mod( 'secondary_company_name', 'Secondary'),
            'tertiary'   => get_theme_mod( 'tertiary_company_name', 'Tertiary'),
            'quaternary' => get_theme_mod( 'quaternary_company_name', 'Quaternary'),
            'fivefold'   => get_theme_mod( 'fivefold_company_name', 'Fivefold'),
        );

        $organizations = array_slice($organizations, 0, $count);

        $wp_customize->add_panel( 'Contacts', array(
            'priority'       => 60,
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
            'title'          => __('Contacts', DOMAIN),
            'description'    => __( 'Contacts information about your company', DOMAIN),
        ) );

        foreach ($organizations as $company_id => $company) {
            add_filter('theme_mod_' . $company_id . '_company_image', __NAMESPACE__ . '\relative_to_absolute');
            $wp_customize->add_section( $company_id . '_contact', array(
                'priority'       => 10,
                'capability'     => 'edit_theme_options',
                'title'          => __($company),
                'description'    =>  __('Add you company\'s contacts', DOMAIN), // 'Добавьте информации о своей организации',
                'panel'  => 'Contacts',
            ) );

            add_company_fields( $wp_customize, $company_id, false );
        }

        $wp_customize->add_section( 'contacts_settings', array(
            'priority'       => 30,
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
            'title'          => __('Configuration', DOMAIN),
            'description'    =>  __('Set a general settings', DOMAIN),
            'panel'  => 'Contacts',
        ) );

        $section = 'contacts_settings';
    }

    $wp_customize->add_setting('companies_count');
    $wp_customize->add_control('companies_count', array(
        'type'     => 'number',
        'label'    => __('Number of companies', DOMAIN),//'Название организации',
        'section'  => $section,
        'input_attrs' => array(
            'min' => 1,
            'max' => 5,
        ),
    ) );
}
