<?php
/**
 * Register plugin actions
 *
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

/**
 * Class Register
 */
class Register {

	/**
	 * Call this method before activate plugin
	 */
	public static function activate() {
	}

	/**
	 * Call this method before disable plugin
	 */
	public static function deactivate() {
	}

	/**
	 * Call this method before delete plugin
	 */
	public static function uninstall() {
	}

	public static function shortcode( $shortcode ) {
		add_shortcode( $shortcode::get_name(), array( $shortcode, 'callback' ) );
	}

	public static function customize_settings( $wp_customize_manager ) {
		$settings = new Settings( 'Contacts', $wp_customize_manager );
		$settings->add_panel();
		$settings->add_settings_section();
		$settings->add_counter_setting();
		$settings->add_schema_setting();
		$settings->add_organizations();
	}
}
