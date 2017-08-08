<?php
namespace Contacts;

function array_map_recursive($callback, $array){
	$func = function ($item) use (&$func, &$callback) {
		return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
	};

	return array_map($func, $array);
}

function sanitize_meta_field( $field ){

	return is_array( $field ) ? array_map_recursive('sanitize_text_field', $field) : sanitize_text_field( $field );
}

class WP_Post_Metabox {
	const SECURE = 'AXpEmw';
	static public $count = 0;

	static protected $fields = array();
	protected $title, $callback, $side, $priority;

	function __construct($title = null, $callback = null, $side = 'normal', $priority = 'normal') {
		if( ! $title || !$callback )
			return;

		$this->title = $title;
		$this->callback = $callback;
		$this->side = ( is_bool($side) && $side ) ? 'side' : $side;
		$this->priority = $priority;
		// if( is_bool($side) && $side ) $this->side = 'side';
		// elseif( is_string($side) ) $this->side = $side;
		// else $this->side = 'advanced';
	}

	function __destruct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		add_filter( 'contacts_sanitize_meta_field', 'Contacts\sanitize_meta_field', 10, 1 );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	static function add_field( $field_name ){
		self::$fields[] = $field_name;
	}

	function add_meta_box( $post_type ){
		// get post types without WP default (for exclude menu, revision..)
		$post_types = get_post_types(array('_builtin' => false));
		$add = array('post', 'page');
		$post_types = array_merge($post_types, $add);

		self::$count++;
		add_meta_box(
			'CustomMetaBox-' . self::$count,
			$this->title,
			$this->callback,
			$post_types,
			$this->side,
			$this->priority,
			array( self::SECURE )
			);
	}

	/**
	 * Сохраняем данные при сохранении поста.
	 *
	 * @param int $post_id ID поста, который сохраняется.
	 */
	function save( $post_id ) {
		$nonce = isset( $_POST[self::SECURE . '_nonce'] ) ? $_POST[self::SECURE . '_nonce'] : false;
		if ( !$nonce ||  ! wp_verify_nonce( $_POST[self::SECURE . '_nonce'], self::SECURE ) )
			return $post_id;

		foreach (self::$fields as $field) {
			if(isset($_POST[$field])){
				$meta = apply_filters( 'contacts_sanitize_meta_field', $_POST[$field] );

				update_post_meta( $post_id, $field, $meta );
			}
			else {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}