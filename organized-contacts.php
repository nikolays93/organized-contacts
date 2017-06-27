<?php
/*
Plugin Name: Organized Contacts
Description: Add site reviews and questions support.
Plugin URI: https://github.com/nikolays93/reviews-and-questions.git
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Version: 1.0 alpha
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// <span class="dashicons dashicons-plus"></span>

define('ORG_SLUG', 'contact');
define('ORG_METANAME', 'contacts');

define('ORG_URL', plugins_url( '', __FILE__ ) );

// define('RQ_TYPE', 'reviews');
define('ORG_DIR', plugin_dir_path( __FILE__ ) );
// define('RQ_PAGE_SLUG', 'reviews_and_questions' );
// define('RQ_META_NAME', '_review_data' );
// define('RQ_HOOK_NAME', 'RQ' );

include_once ORG_DIR . 'class/WPFormRender/class-wp-form-render.php';
include_once ORG_DIR . 'class/class-wp-admin-page-render.php';
include_once ORG_DIR . 'class/class-wp-post-boxes.php';

include_once ORG_DIR . 'inc/post-type.php';
include_once ORG_DIR . 'inc/shortcodes.php';