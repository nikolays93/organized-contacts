<?php
/**
 * @package NikolayS93.OrganizedContacts
 */

namespace NikolayS93\OrganizedContacts;

if ( ! function_exists( __NAMESPACE__ . '\mb_ucfirst' ) ) {
	function mb_ucfirst( $str ) {
		$fc = mb_strtoupper( mb_substr( $str, 0, 1 ) );
		return $fc . mb_substr( $str, 1 );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\strip_spaces' ) ) {
	function strip_spaces( $string ) {
		return preg_replace( '/\s+/', '', $string );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\absolute_to_relative' ) ) {
	function absolute_to_relative( $value ) {
		$value = esc_url( $value );
		$value = str_replace( get_site_url(), '', $value );

		return $value;
	}
}

if ( ! function_exists( __NAMESPACE__ . '\relative_to_absolute' ) ) {
	function relative_to_absolute( $value ) {
		if ( is_string( $value ) ) {
			$value = get_site_url() . $value;
		}

		return $value;
	}
}

if ( ! function_exists( __NAMESPACE__ . '\get_companies' ) ) {
	function get_companies() {
		$organizations = array(
			'primary'   => get_theme_mod( 'primary_contact_name', 'Primary' ),
			'secondary' => get_theme_mod( 'secondary_contact_name', 'Secondary' ),
		);

		$count = get_theme_mod( 'companies_count', 1 );
		$size  = count( $organizations );
		if ( 1 <= $count - $size ) {
			for ( $i = $size + 1; $i <= $count; $i++ ) {
				$contact_id   = "contact_{$i}";
				$contact_name = ucfirst( $contact_id );

				$organizations[ $contact_id ] = get_theme_mod( sprintf( OPTION_KEY, $contact_id, 'name' ), $contact_name );
				if ( $contact_name !== $organizations[ $contact_id ] ) {
					$organizations[ $contact_id ] .= " ($i)";
				}
			}
		} else {
			$organizations = array_slice( $organizations, 0, $count );
		}

		return $organizations;
	}
}
