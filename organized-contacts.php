<?php

/*
Plugin Name: Organized contacts
Plugin URI: https://github.com/nikolays93
Description:
Version: 0.0.1
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

define('OC_LANG', basename(__FILE__, '.php'));

load_plugin_textdomain( OC_LANG, false, basename(__DIR__) . '/languages/' );

require_once __DIR__ . '/includes/shortcodes.php';
require_once __DIR__ . '/includes/customizer.php';
