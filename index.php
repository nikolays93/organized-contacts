<?php
/**
 * Plugin Name: Organized contacts
 * Plugin URI: https://github.com/nikolays93/organized-contacts
 * Description: The plugin allows you to organize information about your companies / organizations
 * Version: 2.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: organized-contacts
 * Domain Path: /languages/
 *
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You shall not pass' );
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Plugin top doc properties.
$plugin_data = get_plugin_data( __FILE__ );

if ( ! defined( __NAMESPACE__ . '\PLUGIN_DIR' ) ) {
	define( __NAMESPACE__ . '\PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( __NAMESPACE__ . 'DOMAIN' ) ) {
	define( __NAMESPACE__ . '\DOMAIN', $plugin_data['TextDomain'] );
}

if ( ! defined( __NAMESPACE__ . 'PREFIX' ) ) {
	define( __NAMESPACE__ . '\PREFIX', DOMAIN . '_' );
}

if ( ! defined( __NAMESPACE__ . 'OPTION_KEY' ) ) {
	define( __NAMESPACE__ . '\OPTION_KEY', '%s_contact_%s' );
}

// load plugin languages.
load_plugin_textdomain( DOMAIN, false, basename( PLUGIN_DIR ) . $plugin_data['DomainPath'] );


require_once PLUGIN_DIR . 'includes/autoload.php';
require_once __DIR__ . '/includes/utils.php';
require_once __DIR__ . '/includes/schema.php';

register_activation_hook( __FILE__, array( Register::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Register::class, 'deactivate' ) );
register_uninstall_hook( __FILE__, array( Register::class, 'uninstall' ) );

/**
 * Register shortcodes
 */
Register::shortcode( new Shortcode_Contact() );
Register::shortcode( new Shortcode_Contact_Image() );

/**
 * Register customizer settings
 */
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_name_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_image_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_city_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_address_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_phone_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_email_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_work_time_field' ), 10, 2 );
add_action( PREFIX . 'add_field', array( Settings::class, 'add_contact_socials_field' ), 10, 2 );

add_action( 'customize_register', array( Register::class, 'customize_settings' ), 10 );

/**
 * Add Schema.org marking
 */
add_filter( PREFIX . 'get_field', __NAMESPACE__ . '\use_schema_field_filter', 10, 3 );
add_filter( PREFIX . 'shortcode_content', __NAMESPACE__ . '\use_schema_wrap_filter', 10, 2 );
