<?php
if (!defined('ABSPATH')) exit;

/**
 * -------------------------------------------------------
 * 1. Add Phone + VAT inside WP Contact Info section
 * (native WordPress fields)
 * -------------------------------------------------------
 */
add_filter('user_contactmethods', function ($methods) {

    $methods['phone'] = 'Τηλέφωνο';
    $methods['vat']   = 'ΑΦΜ';

    return $methods;
});





/**
 * -------------------------------------------------------
 * 2. Show Company / Municipality extra fields
 * directly below Contact Info
 * -------------------------------------------------------
 */
add_action('edit_user_profile', 'sigma_admin_entity_fields', 15);
add_action('show_user_profile', 'sigma_admin_entity_fields', 15);

function sigma_admin_entity_fields($user) {

    $roles = (array) $user->roles;

    // Show only for Company or Municipality users
    if (!array_intersect($roles, ['company', 'municipality'])) {
        return;
    }

    $company_name      = get_user_meta($user->ID, 'company_name', true);
    $municipality_name = get_user_meta($user->ID, 'municipality_name', true);

    ?>

    <h3>Στοιχεία Φορέα</h3>

    <table class="form-table">

        <?php if (in_array('company', $roles)) : ?>
            <tr>
                <th><label for="company_name">Επωνυμία Εταιρείας</label></th>
                <td>
                    <input type="text"
                           name="company_name"
                           id="company_name"
                           value="<?php echo esc_attr($company_name); ?>"
                           class="regular-text">
                </td>
            </tr>
        <?php endif; ?>

        <?php if (in_array('municipality', $roles)) : ?>
            <tr>
                <th><label for="municipality_name">Όνομα Δήμου</label></th>
                <td>
                    <input type="text"
                           name="municipality_name"
                           id="municipality_name"
                           value="<?php echo esc_attr($municipality_name); ?>"
                           class="regular-text">
                </td>
            </tr>
        <?php endif; ?>

    </table>

    <?php
}


/**
 * -------------------------------------------------------
 * 3. Save Company / Municipality fields on Update User
 * (Phone + VAT are saved automatically by WP)
 * -------------------------------------------------------
 */
add_action('personal_options_update', 'sigma_save_admin_entity_fields');
add_action('edit_user_profile_update', 'sigma_save_admin_entity_fields');

function sigma_save_admin_entity_fields($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    if (isset($_POST['company_name'])) {
        update_user_meta(
            $user_id,
            'company_name',
            sanitize_text_field($_POST['company_name'])
        );
    }

    if (isset($_POST['municipality_name'])) {
        update_user_meta(
            $user_id,
            'municipality_name',
            sanitize_text_field($_POST['municipality_name'])
        );
    }
}
