<?php
/**
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

class Settings {
	public $name;
	public $customizer;

	public function __construct( $name, $customizer ) {
		$this->name       = $name;
		$this->customizer = $customizer;
	}

	public function add_panel() {
		$this->customizer->add_panel(
			$this->name,
			array(
				'priority'   => 30,
				'capability' => 'edit_theme_options',
				'title'      => __( 'Contacts', DOMAIN ),
			)
		);
	}

	public function add_settings_section() {
		$this->customizer->add_section(
			'contacts_settings',
			array(
				'priority'       => 30,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Configuration', DOMAIN ),
				'description'    => __( 'Set a general settings', DOMAIN ),
				'panel'          => $this->name,
			)
		);
	}

	public function add_counter_setting() {
		$this->customizer->add_setting(
			'companies_count',
			array(
				'default'   => 1,
				'transport' => 'postMessage',
			)
		);

		$this->customizer->add_control(
			'companies_count',
			array(
				'type'        => 'number',
				'label'       => __( 'Number of companies', DOMAIN ),
				'description' => __( 'Changes will be applied after the page refresh', DOMAIN ),
				'priority'    => '777',
				'section'     => 'contacts_settings',
				'input_attrs' => array(
					'min' => 1,
					'max' => 99,
				),
			)
		);
	}

	public function add_schema_setting() {
		$this->customizer->add_setting(
			'use_schema',
			array(
				'default'   => 0,
				'transport' => 'postMessage',
			)
		);

		$this->customizer->add_control(
			'use_schema',
			array(
				'type'        => 'checkbox',
				'label'       => __( 'Use Schema.org', DOMAIN ),
				'description' => __( 'Fields Shema.org wrap in ', DOMAIN ) . '<br><strong>itemscope itemtype="http://schema.org/LocalBusiness"</strong>',
				'priority'    => '777',
				'section'     => 'contacts_settings',
			)
		);
	}

	public function add_organizations() {
		$organizations = get_companies();

		foreach ( $organizations as $contact_id => $contact ) {
			$this->customizer->add_section(
				$contact_id . '_contact',
				array(
					'priority'   => 10,
					'capability' => 'edit_theme_options',
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'title'      => __( ! empty( $contact ) ? $contact : mb_ucfirst( $contact_id ) ),
					'panel'      => $this->name,
				)
			);

			do_action( PREFIX . 'add_field', $this->customizer, $contact_id );
		}
	}

	public static function add_field( $customizer, $field, $contact_id, $control_args, $setting_args = array() ) {
		$setting_args = wp_parse_args( $setting_args, array( 'transport' => 'postMessage' ) );
		$control_args = wp_parse_args(
			$control_args,
			array(
				'type'    => 'text',
				'section' => $contact_id . '_contact',
			)
		);

		$setting = sprintf( OPTION_KEY, $contact_id, $field );
		$customizer->add_setting( $setting, $setting_args );
		$customizer->add_control( $setting, $control_args );
	}

	public static function add_contact_name_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'name',
			$contact_id,
			array(
				'label'       => __( 'Your contact name', DOMAIN ),
				'priority'    => 10,
				'input_attrs' => array(
					'placeholder' => __( 'Yahoo', DOMAIN ),
				),
			)
		);
	}

	public static function add_contact_image_field( $customizer, $contact_id ) {
		$option_key = sprintf( OPTION_KEY, $contact_id, 'image' );

		add_filter( 'theme_mod_' . $option_key, __NAMESPACE__ . '\relative_to_absolute' );

		$customizer->add_setting(
			$option_key,
			array(
				'sanitize_callback' => __NAMESPACE__ . '\absolute_to_relative',
				'transport'         => 'postMessage',
			)
		);

		$customizer->add_control(
			new \WP_Customize_Image_Control(
				$customizer,
				$option_key,
				array(
					'label'    => __( 'Your contact image', DOMAIN ),
					'section'  => $contact_id . '_contact',
					'priority' => 15,
					'settings' => $option_key,
				)
			)
		);
	}

	public static function add_contact_city_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'city',
			$contact_id,
			array(
				'label'       => __( 'Your contact city (address locality)', DOMAIN ),
				'priority'    => 20,
				'input_attrs' => array(
					'placeholder' => __( 'Paris, France', DOMAIN ),
				),
			)
		);
	}

	public static function add_contact_address_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'address',
			$contact_id,
			array(
				'label'       => __( 'Your contact address', DOMAIN ),
				'priority'    => 30,
				'input_attrs' => array(
					'placeholder' => __( '27 Clarendon Rd, Belfast, UK', DOMAIN ),
				),
			)
		);
	}

	public static function add_contact_phone_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'phone',
			$contact_id,
			array(
				'label'       => __( 'Your contact phone numbers', DOMAIN ),
				'priority'    => 40,
				'input_attrs' => array(
					'placeholder' => '+7 123 456 67 89',
				),
			)
		);
	}

	public static function add_contact_email_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'email',
			$contact_id,
			array(
				'label'       => __( 'Email address', DOMAIN ),
				'priority'    => 50,
				'input_attrs' => array(
					'placeholder' => 'office@example.com',
				),
			)
		);
	}

	public static function add_contact_work_time_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'work_time',
			$contact_id,
			array(
				'type'     => 'textarea',
				'label'    => __( 'Work time mode', DOMAIN ),
				'priority' => 60,
			)
		);
	}

	public static function add_contact_socials_field( $customizer, $contact_id ) {
		self::add_field(
			$customizer,
			'socials',
			$contact_id,
			array(
				'type'     => 'textarea',
				'label'    => __( 'Social links', DOMAIN ),
				'priority' => 70,
			)
		);
	}
}
