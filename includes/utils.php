<?php

namespace CDevelopers\Contacts;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

if( !function_exists('mb_ucfirst') ) {
    function mb_ucfirst($str) {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }
}

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
