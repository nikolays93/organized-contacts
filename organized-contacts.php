<?php
/*
Plugin Name: Контакты (Organized Contacts)
Plugin URI: https://github.com/nikolays93/organized-contacts/
Description: Добавляет возможность управлять контактными данными используя шорткоды.
Version: 2.1.1 alpha
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

// define('CONTACTS_DIR', rtrim(plugin_dir_path( __FILE__ ), '/') );

add_action( 'plugins_loaded', __NAMESPACE__ . '\Init' );
function Init(){
    require_once __DIR__ . '/inc/shortcodes.php';
    require_once __DIR__ . '/inc/customizer.php';
}
