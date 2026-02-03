<?php
if (!defined('ABSPATH')) exit;

/**
 * Add custom fields in WooCommerce Account Details
 */
add_action('woocommerce_edit_account_form', function () {

    $user_id = get_current_user_id();
    $user    = wp_get_current_user();

    $phone = get_user_meta($user_id, 'phone', true);
    $vat   = get_user_meta($user_id, 'vat', true);

    ?>

    <fieldset>
        <legend>Επιπλέον Στοιχεία</legend>
        <?php if (in_array('company', $user->roles)) :

            $company = get_user_meta($user_id, 'company_name', true);
            ?>

            <!-- Company Name -->
            <p class="woocommerce-form-row form-row form-row-wide">
                <label for="company_name">Επωνυμία Εταιρείας</label>
                <input type="text"
                       name="company_name"
                       id="company_name"
                       value="<?php echo esc_attr($company); ?>">
            </p>

        <?php endif; ?>

        <?php if (in_array('municipality', $user->roles)) :

            $municipality = get_user_meta($user_id, 'municipality_name', true);
            ?>

            <!-- Municipality Name -->
            <p class="woocommerce-form-row form-row form-row-wide">
                <label for="municipality_name">Όνομα Δήμου</label>
                <input type="text"
                       name="municipality_name"
                       id="municipality_name"
                       value="<?php echo esc_attr($municipality); ?>">
            </p>

        <?php endif; ?>

        <!-- Phone -->
        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="phone">Τηλέφωνο</label>
            <input type="text"
                   name="phone"
                   id="phone"
                   value="<?php echo esc_attr($phone); ?>">
        </p>

        <?php if (in_array('company', $user->roles) || in_array('municipality', $user->roles)) : ?>

            <!-- VAT -->
            <p class="woocommerce-form-row form-row form-row-wide">
                <label for="vat">ΑΦΜ</label>
                <input type="text"
                       name="vat"
                       id="vat"
                       value="<?php echo esc_attr($vat); ?>">
            </p>

        <?php endif; ?>



    </fieldset>

    <?php
});


/**
 * Save custom fields
 */
add_action('woocommerce_save_account_details', function ($user_id) {

    if (isset($_POST['phone'])) {
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
    }

    if (isset($_POST['vat'])) {
        update_user_meta($user_id, 'vat', sanitize_text_field($_POST['vat']));
    }

    if (isset($_POST['company_name'])) {
        update_user_meta($user_id, 'company_name', sanitize_text_field($_POST['company_name']));
    }

    if (isset($_POST['municipality_name'])) {
        update_user_meta($user_id, 'municipality_name', sanitize_text_field($_POST['municipality_name']));
    }

});

add_action('woocommerce_save_account_details_errors', function ($errors, $user) {

    if (in_array('company', $user->roles)) {
        if (empty($_POST['vat'])) {
            $errors->add('vat_error', 'Το ΑΦΜ είναι υποχρεωτικό για εταιρείες.');
        }
    }

}, 10, 2);

