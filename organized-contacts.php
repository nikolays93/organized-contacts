<?php

/*
Plugin Name: Organized contacts
Plugin URI: https://github.com/nikolays93
Description: The plugin allows you to organize information about your companies / organization
Version: 1.4.1
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

require_once __DIR__ . '/includes/shortcodes.php';
require_once __DIR__ . '/includes/custom-controls.php';
require_once __DIR__ . '/includes/customizer.php';
