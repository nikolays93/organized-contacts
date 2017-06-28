<?php
/*
Plugin Name: Test new page
Plugin URI: 
Description: 
Version: 1.1b
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
namespace PLUGIN_NAME;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('NEW_SLUG', 'option_name');
define('NEW_OPTION', 'option_name');
define('NEW_PLUG_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook(__FILE__, function(){
    // $defaults = array(
    //   'some_option' => 'on',
    //   );

    // add_option( NEW_OPTION, $defaults );
});

require_once NEW_PLUG_DIR . '/inc/company-info.php';
require_once NEW_PLUG_DIR . '/inc/post-type.php';

if(is_admin()){
  require_once NEW_PLUG_DIR . '/inc/class-wp-admin-page-render.php';
  require_once NEW_PLUG_DIR . '/inc/class-wp-form-render.php';
  require_once NEW_PLUG_DIR . '/inc/class-wp-post-boxes.php';
  
  // add_filter( NEW_OPTION . '_columns', function(){return 2;} );

  $page = new WPAdminPageRender( NEW_OPTION,
    array(
      'parent' => 'options-general.php',
      'title' => __('Contacts'),
      'menu' => __('Contacts'),
      ),
    'PLUGIN_NAME\_render_page' );

  // $page->add_metabox( 'handle', 'label', 'PLUGIN_NAME\qq');
  // $page->set_metaboxes();
}

/**
 * Admin Page
 */
// function qq(){
// 	echo "string";
// }
function _render_page(){
  $data = array(
    array(
      'id' => 'few_contacts',
      'type' => 'checkbox',
      'label' => 'Несколько контактов',
      'desc' => 'Использовать несколько контактов',
      ),
    );
  var_dump(get_option( NEW_OPTION ));
  // var_dump(WPForm::active(NEW_OPTION, false, true));
  WPForm::render(
    $data,
    WPForm::active(NEW_OPTION, false, true),
    true,
    array('clear_value' => false)
    );

  submit_button();
}