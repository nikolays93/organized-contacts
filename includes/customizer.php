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
        'label'    => __('Your company name', DOMAIN),
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

    $wp_customize->add_setting($company_id . '_company_city', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_city', array(
        'type'     => 'text',
        'label'    => __('Your company city', DOMAIN),
        'priority' => 25,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_address', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_address', array(
        'type'     => 'text',
        'label'    => __('Your company address', DOMAIN),
        'priority' => 30,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_numbers', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_numbers', array(
        'type'     => 'textarea',
        'label'    => __('Phone numbers', DOMAIN),
        'priority' => 40,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_email', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_email', array(
        'type'     => 'text',
        'label'    => __('Email address', DOMAIN),
        'priority' => 50,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_time_work', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_time_work', array(
        'type'     => 'textarea',
        'label'    => __('Work time mode', DOMAIN),
        'priority' => 60,
        'section'  => $section,
    ) );

    $wp_customize->add_setting($company_id . '_company_socials', array('transport' => 'postMessage'));
    $wp_customize->add_control($company_id . '_company_socials', array(
        'type'     => 'textarea',
        'label'    => __('Social links', DOMAIN),
        'priority' => 70,
        'section'  => $section,
        ) );

    do_action( 'add_company_custom_fields', $wp_customize, $section, $company_id );
}


function customizer($wp_customize) {
    $organizations = get_companies();

    $panel = 'Contacts';

    $wp_customize->add_panel( $panel, array(
        'priority'       => 60,
        'capability'     => 'edit_theme_options',
        'title'          => __($panel, DOMAIN),
    ) );

    $wp_customize->add_section( 'contacts_settings', array(
        'priority'       => 30,
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
        'title'          => __('Configuration', DOMAIN),
        'description'    => __('Set a general settings', DOMAIN),
        'panel'  => $panel,
    ) );

    $wp_customize->add_setting('companies_count', array(
        'default' => 1,
        'transport' => 'postMessage'
        ));
    $wp_customize->add_control('companies_count', array(
        'type'        => 'number',
        'label'       => __('Number of companies', DOMAIN),
        'description' => __('Changes will be applied after the page refresh', DOMAIN),
        'priority'    => '777',
        'section'     => 'contacts_settings',
        'input_attrs' => array(
            'min' => 1,
            'max' => 99,
        ),
    ));

    $wp_customize->add_setting('use_schema', array(
        'default' => 1,
        'transport' => 'postMessage'
        ));
    $wp_customize->add_control('use_schema', array(
        'type'        => 'checkbox',
        'label'       => __('Use Schema.org', DOMAIN),
        'description' => __('Fields Shema.org wrap in ', DOMAIN) . '<br><strong>itemscope itemtype="http://schema.org/LocalBusiness"</strong>',
        'priority'    => '800',
        'section'     => 'contacts_settings',
    ));

    foreach ($organizations as $company_id => $company) {
        add_filter('theme_mod_' . $company_id . '_company_image', __NAMESPACE__ . '\relative_to_absolute');

        $wp_customize->add_section( $company_id . '_contact', array(
            'priority'       => 10,
            'capability'     => 'edit_theme_options',
            'title'          => __(!empty($company) ? $company : mb_ucfirst( $company_id )),
            'description'    =>  __('Add you company\'s contacts', DOMAIN), // 'Добавьте информации о своей организации',
            'panel'  => $panel,
        ) );

        add_company_fields( $wp_customize, $company_id, $company_id . '_contact' );
    }
}
