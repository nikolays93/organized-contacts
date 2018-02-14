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

add_action( 'customize_register', __NAMESPACE__ . '\customizer', 10 );
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

        if( 1 <= $count - 5 ) {
            for ($i=6; $i <= $count; $i++) {
                $company_id = "company_{$i}";
                $company_name = ucfirst($company_id);

                $organizations[ $company_id ] = get_theme_mod( $company_id . '_company_name', $company_name );
                if( $company_name !== $organizations[ $company_id ]  ) {
                    $organizations[ $company_id ] .= " ($i)";
                }
            }
        }
        else {
            $organizations = array_slice($organizations, 0, $count);
        }

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

    $wp_customize->add_setting('companies_count', array(
        'default' => 1,
        'transport' => 'postMessage'
        ));
    $wp_customize->add_control('companies_count', array(
        'type'     => 'number',
        'label'    => __('Number of companies', DOMAIN),//'Название организации',
        'priority' => '777',
        'section'  => $section,
        'input_attrs' => array(
            'min' => 1,
            'max' => 99,
        ),
    ) );
}
