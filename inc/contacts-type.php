<?php

namespace Contacts;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * @todo: redirect to detail if post is lonely
 * @todo: maybe add organization comments as reviews
 */

class PostType {
  /** Static Class */
  private function __clone() {}
  private function __wakeup() {}
  private function __construct() {}

  static function register_post_type(){
    register_post_type( CONTACTS_SLUG, array(
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
          'page-attributes',
          //'excerpt',
          'editor',
          ) ),
      'has_archive'         => true,
      ) );
  }

  static function update_theme_mod($post_id){
    if( get_theme_mod( 'company_primary_id', false ) == $post_id ){
      set_theme_mod( 'company_name', isset($_POST['post_title']) ? sanitize_text_field($_POST['post_title']) : '' );

      if( !isset($_POST['_'.CONTACTS_SLUG]) )
        return $post_id;

      $field = $_POST['_'.CONTACTS_SLUG];//array_filter($_POST['_'.CONTACTS_SLUG], 'sanitize_text_field');
      set_theme_mod( 'company_address', isset($field['address']) ? stripslashes( $field['address'] ) : '' );
      set_theme_mod( 'company_numbers', isset($field['numbers']) ? stripslashes( $field['numbers'] ) : '' );
      set_theme_mod( 'company_email', isset($field['email']) ? stripslashes( $field['email'] ) : '' );
      set_theme_mod( 'company_time_work', isset($field['work-time']) ? stripslashes( $field['work-time'] ) : '' );
      set_theme_mod( 'company_socials', isset($field['socials']) ? stripslashes( $field['socials'] ) : '' );
    }


    return $post_id;
  }

  /********************************* Contacts Meta Boxes ********************************/
  static function add_contacts_metabox(){
      $screen = get_current_screen();
      if( !isset($screen->post_type) || $screen->post_type != CONTACTS_SLUG )
          return false;

      WP_Post_Metabox::add_field( '_' . CONTACTS_SLUG );
      new WP_Post_Metabox('Контакты', array(__CLASS__, 'contacts_metabox_callback'), 'advanced', 'high');
  }

  static function contacts_metabox_callback(){
    $form = array(
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
      WPForm::active('_' . CONTACTS_SLUG, false, true, true),
      true,
      array('admin_page' => '_' . CONTACTS_SLUG)
      );
  }

  static function re_order_contacts_metaboxes(){
      global $post, $wp_meta_boxes;

      if( $post->post_type == CONTACTS_SLUG ){
          do_meta_boxes(get_current_screen(), 'advanced', $post);
          // echo "<span>Описание компании:</span><br>";
          unset($wp_meta_boxes[get_post_type($post)]['advanced']);
      }
  }
}
