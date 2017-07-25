<?php
namespace PLUGIN_NAME;


/**
 * Custom Excerpt Meta Box
 */
// add_action( 'add_meta_boxes' , 'PLUGIN_NAME\remove_postexcerpt_box', 99 );
// add_action( 'add_meta_boxes',  'PLUGIN_NAME\excerpt_box_action' );
// function remove_postexcerpt_box(){

//     remove_meta_box( 'postexcerpt' , NEW_SLUG, 'normal' );
// }
// function excerpt_box_action(){

//     add_meta_box('raq_postexcerpt', __( 'Краткое описание' ), 'PLUGIN_NAME\excerpt_box_custom', NEW_SLUG, 'normal');
// }
// function excerpt_box_custom(){
//     global $post;

//     echo "<label class='screen-reader-text' for='excerpt'> {_('Excerpt')} </label>
//     <textarea rows='1' cols='40' name='excerpt' tabindex='6' id='excerpt'>{$post->post_excerpt}</textarea>";
// }