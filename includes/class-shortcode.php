<?php
/**
 * Plugin shortcode
 *
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

abstract class Shortcode {
	const NAME = self::NAME;

	/** @var array Default fill empty values. */
	public $default;

	/** @var array Current shortcode atts */
	public $atts;

	abstract public function callback( $attributes );

	public static function get_name() {
		return static::NAME;
	}

	public function get_field( $field ) {
		$value = get_theme_mod( sprintf( OPTION_KEY, $this->atts['id'], $field ) );

		return apply_filters( PREFIX . 'get_field', $value, $field, $this->atts );
	}

	public function set_atts( $attributes ) {
		$this->atts = shortcode_atts( $this->default, $attributes, static::NAME );
	}
}
