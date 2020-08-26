<?php
/**
 * Plugin shortcode
 *
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

class Shortcode_Contact extends Shortcode {

	const NAME = 'contact';

	/** @var array Current fields */
	public $fields;

	public function __construct() {
		$this->default = array(
			'id'     => 'primary',
			'field'  => 'name',
			'del'    => ', ',
			'part'   => '',
			'filter' => '',
			'before' => '',
			'after'  => '',
		);
	}

	public function get_field_content() {
		// Get multiply fields keys.
		$field_keys = explode( ',', strip_spaces( $this->atts['field'] ) );
		// Get data by keys.
		$this->fields = array_map( array( __CLASS__, 'get_field' ), $field_keys );

		if ( 1 === count( $field_keys ) && ! empty( $this->atts['part'] ) ) {
			$part   = absint( $this->atts['part'] - 1 );
			$values = explode( $this->atts['del'], current( $this->fields ) );

			$this->fields = array( isset( $values[ $part ] ) ? $values[ $part ] : '' );
		}

		return implode( $this->atts['del'], $this->fields );
	}

	public function callback( $attributes ) {
		// Sanitize attributes.
		$this->set_atts( $attributes );

		$result  = '';
		$content = $this->get_field_content();

		if ( $content ) {
			$result .= $this->atts['before'];
			$result .= apply_filters( PREFIX . 'shortcode_content', $content, $this->atts );
			$result .= $this->atts['after'];
		}

		if ( is_callable( $this->atts['filter'] ) ) {
			$result = apply_filters( $this->atts['filter'], $result );
		}

		return $result;
	}
}
