<?php
/*
Plugin Name: Организованные контакты (Organized Contacts)
Plugin URI: 
Description: Добавляет возможность управлять контактными данными используя шорткоды: [our_address], [our_numbers], [our_email], [our_time_work], [our_socials], [our_first_number]
Version: 1.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action( 'plugins_loaded', function(){
  new ORGContacts();
} );
register_activation_hook( __FILE__, array( 'ORGContacts', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'ORGContacts', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'ORGContacts', 'uninstall' ) );

class ORGContacts {
  const SETTINGS = 'Organized_Contacts';

  public $settings = array();

  private function __clone() {}
  private function __wakeup() {}

  public static function activate(){
    add_option( self::SETTINGS, array() );
  }
  
  public static function uninstall(){
    delete_option(self::SETTINGS);
  }

  function __construct() {
    self::define_constants();
    self::load_classes();
    $this->settings = get_option( self::SETTINGS, array() );

    if( is_admin() ){
      // new WPAdminPageRender(
      //   self::Settings,
      //   array(
      //     'parent' => 'options-general.php',
      //     'title' => __('Project Title'),
      //     'menu' => __('Project Title Menu'),
      //     ),
      //   array($this, 'admin_settings_page')
      //   );
    }
  }

  private static function define_constants(){
    define('CONTACTS_DIR', plugin_dir_path( __FILE__ ) );
  }

  private static function load_classes(){
    require_once CONTACTS_DIR . '/inc/company-info.php';
    /**
     * @todo : require_once CONTACTS_DIR . '/inc/post-type.php';
     */

    if(is_admin()){
      require_once CONTACTS_DIR . '/inc/class-wp-admin-page-render.php';
      require_once CONTACTS_DIR . '/inc/class-wp-form-render.php';
      require_once CONTACTS_DIR . '/inc/class-wp-post-boxes.php';
    }
  }

  function admin_settings_page(){
    // $data = array(
    //   array(
    //     'id' => 'few_contacts',
    //     'type' => 'checkbox',
    //     'label' => 'Несколько контактов',
    //     'desc' => 'Использовать несколько контактов',
    //     ),
    //   );

    // WPForm::render(
    //   $data,
    //   WPForm::active(NEW_OPTION, false, true),
    //   true,
    //   array('clear_value' => false)
    //   );

    // submit_button();
  }
}