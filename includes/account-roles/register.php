<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * --------------------------------------------------
 * 1. Hidden field για customer type
 * (το GSAP / JS αλλάζει το value)
 * --------------------------------------------------
 */
add_action( 'woocommerce_register_form', function () {
    ?>
    <input type="hidden"
           name="sigma_customer_type"
           class="js-auth-role-input"
           value="customer_b2c" />
    <?php
} );


/**
 * --------------------------------------------------
 * 2. VIES VAT validation (Ελλάδα μόνο)
 * --------------------------------------------------
 */
function sigma_validate_vat_vies( $country, $vat ) {

    if ( ! class_exists( 'SoapClient' ) ) {
        return false;
    }

    try {
        $client = new SoapClient(
                'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl',
                [ 'connection_timeout' => 5 ]
        );

        $result = $client->checkVat([
                'countryCode' => $country,
                'vatNumber'   => preg_replace( '/\D/', '', $vat ),
        ]);

        return ! empty( $result->valid );

    } catch ( Exception $e ) {
        return false;
    }
}


/**
 * --------------------------------------------------
 * 3. Validation κατά την εγγραφή
 * --------------------------------------------------
 */
add_filter( 'woocommerce_registration_errors', function ( $errors ) {

    $type = $_POST['sigma_customer_type'] ?? '';

    // --- Τηλέφωνο (όλοι)
// --- Τηλέφωνο (όλοι)
    if ( empty( $_POST['phone'] ) ) {

        $errors->add(
                'phone_required',
                __( 'Το τηλέφωνο είναι υποχρεωτικό.', 'ruined' )
        );

    } else {

        $phone = preg_replace( '/\D/', '', $_POST['phone'] );

        // Ελλάδα: 10 ψηφία
        if ( strlen( $phone ) !== 10 ) {
            $errors->add(
                    'phone_format',
                    __( 'Το τηλέφωνο πρέπει να έχει 10 ψηφία.', 'ruined' )
            );
        }
    }


    // --- Customer (B2C)
    if ( $type === 'customer_b2c' && empty( $_POST['customer_name'] ) ) {
        $errors->add(
                'name_required',
                __( 'Το όνομα είναι υποχρεωτικό.', 'ruined' )
        );
    }

    // --- Company & Municipality
    if ( in_array( $type, [ 'company', 'municipality' ], true ) ) {

        // Επωνυμία
        if ( $type === 'company' && empty( $_POST['company_name'] ) ) {
            $errors->add(
                    'company_required',
                    __( 'Η επωνυμία εταιρείας είναι υποχρεωτική.', 'ruined' )
            );
        }

        if ( $type === 'municipality' && empty( $_POST['municipality_name'] ) ) {
            $errors->add(
                    'municipality_required',
                    __( 'Το όνομα δήμου είναι υποχρεωτικό.', 'ruined' )
            );
        }

        // VAT
        if ( empty( $_POST['vat'] ) ) {
            $errors->add(
                    'vat_required',
                    __( 'Το ΑΦΜ είναι υποχρεωτικό.', 'ruined' )
            );
        } else {

            $vat = preg_replace( '/\D/', '', $_POST['vat'] );

            // Ελλάδα → 9 ψηφία
            if ( strlen( $vat ) !== 9 ) {
                $errors->add(
                        'vat_format',
                        __( 'Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.', 'ruined' )
                );
            } else {

                // VIES validation
                if ( ! sigma_validate_vat_vies( 'EL', $vat ) ) {
                    $errors->add(
                            'vat_invalid',
                            __( 'Το ΑΦΜ δεν είναι έγκυρο στο VIES.', 'ruined' )
                    );
                }
            }
        }
    }

    return $errors;
} );


/**
 * --------------------------------------------------
 * 4. Save user meta + assign role
 * --------------------------------------------------
 */
add_action( 'woocommerce_created_customer', function ( $user_id ) {

    if ( empty( $_POST['sigma_customer_type'] ) ) {
        return;
    }

    $type = sanitize_text_field( $_POST['sigma_customer_type'] );
    $user = new WP_User( $user_id );

    // Assign role
    switch ( $type ) {
        case 'company':
            $user->set_role( 'company' );
            break;

        case 'municipality':
            $user->set_role( 'municipality' );
            break;

        default:
            $user->set_role( 'customer_b2c' );
    }

    update_user_meta( $user_id, 'sigma_customer_type', $type );

    // Phone (όλοι)
    if ( ! empty( $_POST['phone'] ) ) {
        update_user_meta(
                $user_id,
                'phone',
                sanitize_text_field( $_POST['phone'] )
        );
    }

    // Customer name
    if ( ! empty( $_POST['customer_name'] ) ) {
        update_user_meta(
                $user_id,
                'customer_name',
                sanitize_text_field( $_POST['customer_name'] )
        );
    }

    // Company name
    if ( ! empty( $_POST['company_name'] ) ) {
        update_user_meta(
                $user_id,
                'company_name',
                sanitize_text_field( $_POST['company_name'] )
        );
    }

    // Municipality name
    if ( ! empty( $_POST['municipality_name'] ) ) {
        update_user_meta(
                $user_id,
                'municipality_name',
                sanitize_text_field( $_POST['municipality_name'] )
        );
    }

    // VAT
    if ( ! empty( $_POST['vat'] ) ) {
        update_user_meta(
                $user_id,
                'vat',
                sanitize_text_field( $_POST['vat'] )
        );

        // Flag ότι έχει ελεγχθεί
        update_user_meta( $user_id, 'vat_verified', true );
    }

}, 20 );


/**
 * --------------------------------------------------
 * 5. Redirect μετά το login
 * --------------------------------------------------
 */
add_filter( 'woocommerce_login_redirect', function ( $redirect, $user ) {

    if ( in_array( 'company', (array) $user->roles, true ) ) {
        return home_url( '/company-dashboard/' );
    }

    if ( in_array( 'municipality', (array) $user->roles, true ) ) {
        return home_url( '/municipality-dashboard/' );
    }

    return wc_get_page_permalink( 'myaccount' );

}, 10, 2 );


/**
 * --------------------------------------------------
 * 6. Flag για άνοιγμα modal αν υπάρχουν errors
 * --------------------------------------------------
 */
add_action( 'wp_footer', function () {
    if ( wc_notice_count( 'error' ) > 0 ) : ?>
        <script>
            window.__SIGMA_AUTH_HAS_ERRORS__ = true;
        </script>
    <?php endif;
} );
