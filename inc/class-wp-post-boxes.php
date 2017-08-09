<?php
namespace Contacts;

function array_map_recursive($callback, $array){
	$func = function ($item) use (&$func, &$callback) {
		return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
	};

	return array_map($func, $array);
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

		add_action( 'save_post', array( $this, 'save' ) );
	}

	static function add_field( $field_name ){
		self::$fields[] = $field_name;
	}

	function callback_with_nonce(){
		call_user_func($this->callback);
		wp_nonce_field( WP_Post_Metabox::SECURE, WP_Post_Metabox::SECURE );
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
			array($this, 'callback_with_nonce'),
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
		if( !isset( $_POST[self::SECURE] ) )
			return $post_id;

		if ( ! wp_verify_nonce( $_POST[self::SECURE], self::SECURE ) )
			return $post_id;

		foreach (self::$fields as $field) {
			if(isset($_POST[$field])){
				$meta = $_POST[$field];

				update_post_meta( $post_id, $field, $meta );
			}
			else {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}