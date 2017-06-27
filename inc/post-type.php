<?php
/**
 * Register Post Type
 */
add_action('init', 'register_contacts_post_type');
function register_contacts_post_type(){
  /**
   * @todo: add organization comments as reviews
   */  
  register_post_type( ORG_SLUG, array(
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
    'public'              => false,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => true,
    // 'show_in_rest'        => null, // добавить в REST API. C WP 4.7
    // 'rest_base'           => null, // $post_type. C WP 4.7
    'menu_position'       => 58,
    'menu_icon'           => 'dashicons-admin-home', 
    //'capability_type'   => 'post',
    //'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
    //'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
    'hierarchical'        => false,
    'supports'            => apply_filters( 'contacts_post_supports',
      array(
        'title',
        //'editor',
        'thumbnail',
        'excerpt'
        ) ),
    // 'taxonomies'          => array(),
    'has_archive'         => false,
    // 'rewrite'             => true,
    // 'query_var'           => true,
  ) );
}

/**
 * After contacts post save
 */
add_action( 'save_post', 'save_contact_ids_option' );
function save_contact_ids_option( $post_id ) {
  if ( ORG_SLUG != $_POST['post_type'] || wp_is_post_revision( $post_id ) )
    return $post_id;

  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;

  $args = array(
    'posts_per_page'   => -1,
    'orderby'          => 'date',
    'order'            => 'ASC',
    'post_type'        => ORG_SLUG,
    'post_status'      => 'publish',
    );
  $posts_array = get_posts( $args );

  $contact_ids = array();
  foreach ($posts_array as $_post) {
    $contact_ids[] = $_post->ID;
  }
  update_option( 'contact_ids', $contact_ids );
}

/**
 * @todo redirect to detail if post is lonely
 */

/**
 * Re-Order All Metabox
 */
add_action('edit_form_after_title', 'resort_boxes' );
add_action( 'load-post.php',     'metabox_action' );
add_action( 'load-post-new.php', 'metabox_action' );
function resort_boxes(){
    global $post, $wp_meta_boxes;

    if( $post->post_type == ORG_SLUG ){
        do_meta_boxes(get_current_screen(), 'advanced', $post);
        unset($wp_meta_boxes[get_post_type($post)]['advanced']);
    }
}
function metabox_action(){
    $screen = get_current_screen();
    if( !isset($screen->post_type) || $screen->post_type != ORG_SLUG )
        return false;

    $boxes = new RQ\WPPostBoxes();
    $boxes->add_box('Контакты', 'metabox_render', false, 'high' );
    $boxes->add_fields( ORG_METANAME );
}
function metabox_render($post, $data){
  $form =array(
    array(
      'id'      => 'city',
      'type'    => 'text',
      'label'   => 'Город',
      // 'class'   => 'widefat',
      // 'desc'    => '',
      ),
    array(
      'id'      => 'address',
      'type'    => 'textarea',
      'label'   => 'Адрес',
      'class'   => 'widefat',
      'desc'    => '',
      ),
    array(
      'id'      => 'phones',
      'type'    => 'text',
      'label'   => 'Номер телефона',
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
    WPForm::active(ORG_METANAME, false, true, true),
    true,
    array(
      'clear_value' => false,
      'admin_page' => ORG_METANAME,
      )
    );
  wp_nonce_field( $data['args'][0], $data['args'][0].'_nonce' );
}

/**
 * Custom Excerpt Meta Box
 */
add_action( 'add_meta_boxes' , 'remove_postexcerpt_box', 99 );
add_action( 'add_meta_boxes',  'excerpt_box_action' );
function remove_postexcerpt_box(){

    remove_meta_box( 'postexcerpt' , ORG_SLUG, 'normal' );
}
function excerpt_box_action(){

    add_meta_box('raq_postexcerpt', __( 'Краткое описание' ), 'excerpt_box_custom', ORG_SLUG, 'normal');
}
function excerpt_box_custom(){
    global $post;

    echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
    <textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
}