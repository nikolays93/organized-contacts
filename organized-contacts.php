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

/**
 * @todo : add archive page
 */
if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * Shortcodes: for easy use
 *
 * [our_address]
 * [our_numbers]
 * [our_email]
 * [our_time_work]
 * [our_socials]
 * [our_first_number]
 */

add_action( 'plugins_loaded', function(){ ORGContacts::get_instance()->init(); } );
register_activation_hook( __FILE__, array( 'ORGContacts', 'activate' ) );
register_uninstall_hook( __FILE__, array( 'ORGContacts', 'uninstall' ) );

class ORGContacts {
  const SETTINGS = 'Organized_Contacts';
  const SLUG = 'contacts';
  const ORGS_ID = 'contacts_id';

  public static $settings = array();

  private function __construct() {}
  private function __clone() {}
  private function __wakeup() {}

  private static $instance = null;
  public static function get_instance() {
    if ( ! self::$instance )
      self::$instance = new self;
    return self::$instance;
  }

  public static function activate(){ add_option( self::SETTINGS, array() ); }
  public static function uninstall(){ delete_option(self::SETTINGS); }

  function init(){
    self::define_constants();
    self::load_classes();
    self::$settings = get_option( self::SETTINGS, array() );

    if( empty(self::$settings['advanced_contacts']) ){
      /* Add Customizer */
      require_once CONTACTS_DIR . '/inc/company-info.php';
    }
    else {
      /* Post Type */
      add_action('init', array($this, 'register_contacts_post_type'));
      add_action( 'save_post', array($this, 'update_contacts_id') );
      /* Meta Box */
      add_action( 'load-post.php',     array($this, 'contacts_metabox') );
      add_action( 'load-post-new.php', array($this, 'contacts_metabox') );
      add_action('edit_form_after_title', array($this, 're_order_contacts_metaboxes') );
    }

    if( is_admin() ){
      /* Settings page */
      new WPAdminPageRender(
        self::SETTINGS,
        array(
          'parent' => 'options-general.php',
          'title' => __('Organized Contacts'),
          'menu' => __('Contacts'),
          ),
        array($this, 'admin_settings_page'),
        false,
        array($this, 'admin_validate_page')
        );
    }
  }

  private static function define_constants(){
    define('CONTACTS_DIR', plugin_dir_path( __FILE__ ) );
  }

  private static function load_classes(){
    if(is_admin()){
      require_once CONTACTS_DIR . '/inc/class-wp-admin-page-render.php';
      require_once CONTACTS_DIR . '/inc/class-wp-form-render.php';
      require_once CONTACTS_DIR . '/inc/class-wp-post-boxes.php';
    }
  }

  /************************************** ADMIN PAGE **************************************/
  function admin_settings_page(){
    $data = array(
      array(
        'id' => 'advanced_contacts',
        'type' => 'checkbox',
        'label' => 'Расширенная информация об организаци(ях)и',
        'desc' => '',
        ),
      );

    WPForm::render(
      $data,
      WPForm::active(self::SETTINGS, false, true),
      true,
      array('clear_value' => false, 'admin_page' => true)
      );

    submit_button();
  }

  function admin_validate_page( $inputs ){
    if( !is_array($inputs) )
      return '';

    // default filters
    $inputs = array_map_recursive( 'sanitize_text_field', $inputs );
    $inputs = array_filter_recursive($inputs);

    file_put_contents(__DIR__ . '/debug.log', print_r($inputs, 1) );
    /* Create First Organization if not exists */
    $query = new WP_Query( array(
      'post_type' => self::SLUG,
      'posts_per_page' => 1,
      ) );

    if ( !$query->have_posts() && !empty($inputs['advanced_contacts']) ) {
      $new_post = array(
        'post_author'    => 1,
        'post_content'   => '',
        'post_excerpt'   => '',
        'post_status'      => 'publish',
        //'post_name'      => slug,
        'post_title'     => 'Наши контакты',
        'post_type'      => self::SLUG,
        'meta_input'     => array(
          '_contacts' => array(
            'address'   => get_theme_mod( 'company_address' ),
            'numbers'   => get_theme_mod( 'company_numbers' ),
            'email'     => get_theme_mod( 'company_email' ),
            'work-time' => get_theme_mod( 'company_time_work' ),
            'socials'   => get_theme_mod( 'company_socials' ),
            )
          ),
        );
      $post_id = wp_insert_post( $new_post, true );

      update_option( self::ORGS_ID, array($post_id => 'Наши контакты') );
    }

    return $inputs;
  }

  /********************************** CONTACTS POST TYPE **********************************/
  function register_contacts_post_type(){
    /**
     * @todo: add organization comments as reviews
     */  
    register_post_type( self::SLUG, array(
      'label'  => 'Контакты',
      'labels' => array(
        'name'               => 'Контакты',
        'singular_name'      => 'Контакт',
        'add_new'            => 'Добавить контакт',
        'add_new_item'       => 'Добавление контакта',
        'edit_item'          => 'Редактирование контакта',
        'new_item'           => 'Новый контакт',
        'view_item'          => 'Смотреть контакт',
        'search_items'       => 'Искать контакт',
        'not_found'          => 'Не найдено',
        'not_found_in_trash' => 'Не найдено в корзине',
        'parent_item_colon'  => '',
        'menu_name'          => 'Контакты',
      ),
      'description'         => 'Контактная информация вашей организации',
      'public'              => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_admin_bar'   => false,
      'show_in_nav_menus'   => true,
      'menu_position'       => 58,
      'menu_icon'           => 'dashicons-admin-home', 
      'hierarchical'        => false,
      'supports'            => apply_filters( 'contacts_post_supports',
        array(
          'title',
          'thumbnail',
          //'excerpt',
          'editor',
          ) ),
      'has_archive'         => false,
    ) );
  }

  function update_contacts_id( $post_id ) {
    if ( !isset($_POST['post_type']) || self::SLUG != $_POST['post_type'] || wp_is_post_revision( $post_id ) )
      return $post_id;

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
      return $post_id;

    $args = array(
      'posts_per_page'   => -1,
      'orderby'          => 'date',
      'order'            => 'ASC',
      'post_type'        => self::SLUG,
      'post_status'      => 'publish',
      );
    $posts_array = get_posts( $args );

    $contact_ids = array();
    foreach ($posts_array as $_post) {
      $contact_ids[$_post->ID] = $_post->post_title;
    }
    update_option( self::ORGS_ID, $contact_ids );
  }

  function contacts_metabox(){
      $screen = get_current_screen();
      if( !isset($screen->post_type) || $screen->post_type != self::SLUG )
          return false;

      $boxes = new WPPostBoxes();
      $boxes->add_box('Контакты', array($this, 'contacts_metabox_callback'), false, 'high' );
      $boxes->add_fields( '_' . self::SLUG );
  }

  function contacts_metabox_callback($post, $data){
    $form =array(
      // array(
      //   'id'      => 'city',
      //   'type'    => 'text',
      //   'label'   => 'Город',
      //   // 'class'   => 'widefat',
      //   // 'desc'    => '',
      //   ),
      array(
        'id'      => 'address',
        'type'    => 'textarea',
        'label'   => 'Адрес',
        'class'   => 'widefat',
        'desc'    => '',
        ),
      array(
        'id'      => 'numbers',
        'type'    => 'textarea',
        'label'   => 'Номера телефонов',
        'rows'    => 3,
        'desc'    => '',
        'multyple'=> true,
        ),
      array(
        'id'      => 'email',
        'type'    => 'text',
        'label'   => 'Email',
        'desc'    => '',
        ),
      array(
        'id'      => 'work-time',
        'type'    => 'textarea',
        'label'   => 'Режим работы',
        'class'   => 'widefat',
        'desc'    => '',
        ),
      array(
        'id'      => 'socials',
        'type'    => 'text',
        'label'   => 'Социальные сети',
        'desc'    => '',
        'class'   => 'widefat',
        'multyple'=> true,
        )
      );

    WPForm::render(
      $form,
      WPForm::active('_' . self::SLUG, false, true, true),
      true,
      array(
        'clear_value' => false,
        'admin_page' => '_' . self::SLUG,
        )
      );
    wp_nonce_field( $data['args'][0], $data['args'][0].'_nonce' );
  }
  
  function re_order_contacts_metaboxes(){
      global $post, $wp_meta_boxes;

      if( $post->post_type == self::SLUG ){
          do_meta_boxes(get_current_screen(), 'advanced', $post);
          // label after @contacts (for editor)
          echo "<span>Описание компании:</span><br>";
          unset($wp_meta_boxes[get_post_type($post)]['advanced']);
      }
  }

/**
 * @todo redirect to detail if post is lonely
 */
}

/********************************** SHORTCODES **********************************/
function get_company_info( $field, $filter = 'the_content' ){
  global $post;

  if( empty(ORGContacts::$settings['advanced_contacts']) ){
    $info = get_theme_mod( 'company_' . $field );
  }
  else {
    $ids = get_option( ORGContacts::ORGS_ID, array() );
    if( isset($post->ID) && array_key_exists($post->ID, $ids) ){
      $contact_id = $post->ID;
    }
    else {
      reset($ids);
      $contact_id = key($ids);
    }

    if( !$contact_id )
      return false;

    $all_data = get_post_meta( $contact_id, '_'.ORGContacts::SLUG, true );
    $info = isset($all_data[$field]) ? $all_data[$field] : ' ';
  }
  
  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
  elseif( $filter )
    return apply_filters( $filter, $info );

  return $info;
}

function get_company_address(){ return get_company_info('address'); }
function get_company_numbers(){ return get_company_info('numbers'); }
function get_company_time_work(){ return get_company_info('time_work'); }
function get_company_email(){ return get_company_info('email'); }
function get_company_socials(){ return get_company_info('socials'); }
function get_company_number( $del = ',', $num=0, $filter = 'the_content' ) {
// for shortcode
  if(! $del) $del = ',';
  if(! $num) $num = 0;
  if(! $filter || $filter != false) $filter = 'the_content';

  $numbers = get_company_info('numbers', false);
  $info = explode($del, $numbers);

  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info[$num] )) );
  elseif( $filter )
    return apply_filters( $filter, $info[$num] );

  return $info[$num];
}
add_shortcode('our_address', 'get_company_address');
add_shortcode('our_numbers', 'get_company_numbers');
add_shortcode('our_first_number', 'get_company_number');
add_shortcode('our_time_work', 'get_company_time_work');
add_shortcode('our_email', 'get_company_email');
add_shortcode('our_socials', 'get_company_socials');
