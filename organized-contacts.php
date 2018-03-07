<?php

/*
Plugin Name: Organized contacts
Plugin URI: https://github.com/nikolays93
Description: The plugin allows you to organize information about your companies / organization
Version: 1.5
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

const DOMAIN = 'organized-contacts';

load_plugin_textdomain( DOMAIN, false, DOMAIN . '/languages/' );

function get_companies() {
    $organizations = array(
        'primary'    => get_theme_mod( 'primary_company_name', 'Primary' ),
        'secondary'  => get_theme_mod( 'secondary_company_name', 'Secondary'),
    );

    $count = get_theme_mod('companies_count', 1);
    $size = sizeof($organizations);
    if( 1 <= $count - $size ) {
        for ($i = $size + 1; $i <= $count; $i++) {
            $company_id = "company_{$i}";
            $company_name = ucfirst($company_id);

            $organizations[ $company_id ] = get_theme_mod( $company_id . '_company_name', $company_name );
            if( $company_name !== $organizations[ $company_id ]  )
                $organizations[ $company_id ] .= " ($i)";
        }
    }
    else {
        $organizations = array_slice($organizations, 0, $count);
    }

    return $organizations;
}

require_once __DIR__ . '/includes/shortcodes.php';
require_once __DIR__ . '/includes/custom-controls.php';
require_once __DIR__ . '/includes/customizer.php';
