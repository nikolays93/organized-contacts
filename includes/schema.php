<?php
/**
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

function use_schema_field_filter( $value, $field, $atts ) {
	if ( ! $value || ! get_theme_mod( 'use_schema', false ) ) {
		return $value;
	}

	switch ( $field ) {
		case 'name':
			$value = '<span itemprop="name">' . $value . '</span>';
			break;

		/**
		 * @todo what u think about it?
		 * <span itemprop="postalCode">119021</span>
		 */
		case 'city':
			$value = '<span itemprop="addressLocality">' . $value . '</span>';
			break;

		case 'address':
			$value = '<span itemprop="streetAddress">' . $value . '</span>';
			break;

		case 'numbers':
			$value = '<span itemprop="telephone">' . $value . '</span>';
			break;

		case 'email':
			$value = '<span itemprop="email">' . $value . '</span>';
			break;

		case 'work_time':
			/**
			 * @todo after set datepicker
			 * datetime="Tu,Th 16:00−20:00"
			 */
			$value = '<time itemprop="openingHours">' . $value . '</time>';
			break;

		case 'socials':
			break;
	}

	return $value;
}

function use_schema_wrap_filter( $content, $atts ) {
	if ( false !== strpos( $atts['field'], 'city' ) || false !== strpos( $atts['field'], 'address' ) ) {
		$content = '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' . $content . '</span>';
	}

	return $content;
}
