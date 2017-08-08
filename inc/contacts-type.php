<?php

namespace Contacts;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * @todo redirect to detail if post is lonely
 */

class PostType {
  static public $slug;
  /** Static Class */
  private function __clone() {}
  private function __wakeup() {}
  private function __construct() {}
  // private static $instance = null;
  // public static function get_instance() {
  //   if ( ! self::$instance )
  //     self::$instance = new self;
  //   return self::$instance;
  // }

  // function update_contacts_id( $post_id ) {
  //   if ( !isset($_POST['post_type']) || self::SLUG != $_POST['post_type'] || wp_is_post_revision( $post_id ) )
  //     return $post_id;

  //   if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
  //     return $post_id;

  //   $args = array(
  //     'posts_per_page'   => -1,
  //     'orderby'          => 'date',
  //     'order'            => 'ASC',
  //     'post_type'        => self::SLUG,
  //     'post_status'      => 'publish',
  //     );
  //   $posts_array = get_posts( $args );

  //   $contact_ids = array();
  //   foreach ($posts_array as $_post) {
  //     $contact_ids[$_post->ID] = $_post->post_title;
  //   }
  //   update_option( self::ORGS_ID, $contact_ids );
  // }

  static function register_post_type(){
    /**
     * @todo: maybe add organization comments as reviews
     */
    register_post_type( self::$slug, array(
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

  static function add_contacts_metabox(){
      $screen = get_current_screen();
      if( !isset($screen->post_type) || $screen->post_type != self::$slug )
          return false;

      Contacts\WP_Post_Metabox::add_field( '_' . self::$slug );
      Contacts\WP_Post_Metabox::add_field( '_primary' );
      new Contacts\WP_Post_Metabox('Контакты', array(__CLASS__, 'contacts_metabox_callback'), 'advanced', 'high');
      new Contacts\WP_Post_Metabox('Сделать главными', array(__CLASS__, 'contacts_metabox_callback_side'), 'side', 'low' );
  }

  /********************************* Contacts Meta Boxes ********************************/
  static function contacts_metabox_callback($post, $data){
    // var_dump(get_post_meta($_GET['post']));
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

  static function contacts_metabox_callback_side(){
    WPForm::render(
      array(
        'id'      => '_primary',
        'type'    => 'checkbox',
        'label'   => 'Сделать эти контакты главными',
        ),
      array( '_primary' => isset($_GET['post']) ? get_post_meta( absint($_GET['post']), '_primary', true )  : false ),
      true,
      array(
        'clear_value' => false,
        // 'admin_page' => '_primary',
        )
      );
  }

  static function re_order_contacts_metaboxes(){
      global $post, $wp_meta_boxes;

      if( $post->post_type == self::$slug ){
          do_meta_boxes(get_current_screen(), 'advanced', $post);
          // echo "<span>Описание компании:</span><br>";
          unset($wp_meta_boxes[get_post_type($post)]['advanced']);
      }
  }
}
