<?php
/**
 * Plugin shortcode
 *
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

class Shortcode_Contact_Image extends Shortcode {

	const NAME = 'contact_image';

	public function __construct() {
		$this->default = array(
			'id'     => 'primary',
			'field'  => 'image',
			'filter' => '',
			'before' => '',
			'after'  => '',
		);
	}

	public function callback( $attributes ) {
		// Sanitize attributes.
		$this->set_atts( $attributes );

		$result    = '';
		$image_url = static::get_field( $this->atts['field'] );

		if ( $image_url ) {
			$result .= $this->atts['before'];
			$result .= sprintf(
				'<img src="%s" alt="%s">',
				static::get_field( $this->atts['field'] ),
				get_theme_mod( sprintf( OPTION_KEY, $this->atts['id'], 'name' ) )
			);

			$result .= $this->atts['after'];
		}

		if ( is_callable( $this->atts['filter'] ) ) {
			return apply_filters( $this->atts['filter'], $result );
		}

		return $result;
	}
}
