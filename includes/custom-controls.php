<?php

namespace CDevelopers\Contacts;

class CustomControl
{
    private $tmp = array();

    function __construct($id, $args = array(), $custom_control = false)
    {
        $this->tmp['id'] = sanitize_text_field( $id );
        $this->tmp['args'] = wp_parse_args( $args, array(
            'type'    => 'text',
            'label'   => '',
            'priority'=> 100,
        ) );

        $this->tmp['control'] = ( class_exists($custom_control) ) ? $custom_control : false;
        if( $this->tmp['control'] ) {
            unset( $this->tmp['args']['type'] );
        }

        add_action( 'add_company_custom_fields', array($this, 'company_custom_fields'), 10, 3 );
    }

    function company_custom_fields( $wp_customize, $section, $company_id ) {
        $this->tmp['args']['section'] = $section;

        if( ! $this->tmp['control'] ) {
            $wp_customize->add_setting($company_id . '_' . $this->tmp['id'], array('transport' => 'postMessage'));
            $wp_customize->add_control($company_id . '_' . $this->tmp['id'], $this->tmp['args'] );
        }
        else {
            $this->tmp['args']['settings'] = $company_id . '_' . $this->tmp['id'];
            $wp_customize->add_setting($company_id . '_' . $this->tmp['id'], array('transport' => 'postMessage'));
            $wp_customize->add_control(
                new $this->tmp['control'](
                    $wp_customize,
                    $company_id . '_' . $this->tmp['id'],
                    $this->tmp['args']
                )
            );
        }
    }
}
