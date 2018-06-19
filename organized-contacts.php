<?php

/*
Plugin Name: Organized contacts
Plugin URI: https://github.com/nikolays93
Description: The plugin allows you to organize information about your companies / organization
Version: 1.5
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

const DOMAIN = 'organized-contacts';

load_plugin_textdomain( DOMAIN, false, DOMAIN . '/languages/' );

require_once __DIR__ . '/includes/utils.php';
require_once __DIR__ . '/includes/shortcodes.php';
require_once __DIR__ . '/includes/mce.php';
require_once __DIR__ . '/includes/custom-controls.php';
require_once __DIR__ . '/includes/customizer.php';

/**
 * Add shortcode [company filed="%s"]
 *
 * @param  field   name | address | numbers | email | time_work | socials *required
 * @param  id      Company ID: primary | secondary | company_%d (default: primary)
 * @param  filter  set wordrpess filter (default: the_content)
 * @param  before  text before
 * @param  after   text after
 */
add_shortcode('company', __NAMESPACE__ . '\company_info_shortcode');

/**
 * Add shortcode [phone]
 *
 * @param  id      Company ID: primary | secondary | company_%d (default: primary)
 * @param  filter  set wordrpess filter (default: the_content)
 * @param  del     phones delimiter (default: ,)
 * @param  num     number count
 * @param  before  text before
 * @param  after   text after
 */
add_shortcode('phone', __NAMESPACE__ . '\get_company_number');

/**
 * Register customizer settings
 */
add_action( 'customize_register', __NAMESPACE__ . '\customizer', 10 );

/**
 * Add mce scripts
 */
// if ( user_can_richedit() ) {
    add_filter("mce_external_plugins", __NAMESPACE__ . '\mce_plugin');
    add_filter("mce_buttons", __NAMESPACE__ . '\mce_button');
    add_action("admin_head", __NAMESPACE__ . '\mce_enqueue');
// }

