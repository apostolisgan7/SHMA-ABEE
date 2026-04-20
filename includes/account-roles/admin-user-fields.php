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
 * 2. Show Company / Municipality extra fields + Approval
 * -------------------------------------------------------
 */
add_action('edit_user_profile', 'sigma_admin_entity_fields', 15);
add_action('show_user_profile', 'sigma_admin_entity_fields', 15);

function sigma_admin_entity_fields($user) {

    $roles = (array) $user->roles;

    // Εμφάνιση μόνο για Company ή Municipality
    if (!array_intersect($roles, ['company', 'municipality'])) {
        return;
    }

    $company_name      = get_user_meta($user->ID, 'company_name', true);
    $municipality_name = get_user_meta($user->ID, 'municipality_name', true);
    $status            = get_user_meta($user->ID, '_sigma_account_status', true) ?: 'approved';

    ?>
    <hr />
    <h3>Έγκριση Λογαριασμού</h3>
    <table class="form-table">
        <tr>
            <th><label for="sigma_account_status">Κατάσταση Λογαριασμού</label></th>
            <td>
                <select name="sigma_account_status" id="sigma_account_status">
                    <option value="pending" <?php selected($status, 'pending'); ?>>Pending (Σε αναμονή)</option>
                    <option value="approved" <?php selected($status, 'approved'); ?>>Approved (Ενεργός)</option>
                </select>
                <p class="description">Αν αλλάξετε την κατάσταση σε "Approved", ο χρήστης θα λάβει αυτόματο email ενημέρωσης.</p>
            </td>
        </tr>
    </table>

    <h3>Στοιχεία Φορέα</h3>
    <table class="form-table">
        <?php if (in_array('company', $roles)) : ?>
            <tr>
                <th><label for="company_name">Επωνυμία Εταιρείας</label></th>
                <td>
                    <input type="text" name="company_name" id="company_name" value="<?php echo esc_attr($company_name); ?>" class="regular-text">
                </td>
            </tr>
        <?php endif; ?>

        <?php if (in_array('municipality', $roles)) : ?>
            <tr>
                <th><label for="municipality_name">Όνομα Δήμου</label></th>
                <td>
                    <input type="text" name="municipality_name" id="municipality_name" value="<?php echo esc_attr($municipality_name); ?>" class="regular-text">
                </td>
            </tr>
        <?php endif; ?>
    </table>
    <?php
}

/**
 * -------------------------------------------------------
 * 3. Save fields & Send Approval Email
 * -------------------------------------------------------
 */
add_action('personal_options_update', 'sigma_save_admin_entity_fields');
add_action('edit_user_profile_update', 'sigma_save_admin_entity_fields');

function sigma_save_admin_entity_fields($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    // Διαχείριση Status & Email
    if (isset($_POST['sigma_account_status'])) {
        $old_status = get_user_meta($user_id, '_sigma_account_status', true);
        $new_status = sanitize_text_field($_POST['sigma_account_status']);

        update_user_meta($user_id, '_sigma_account_status', $new_status);

        // Αν έγινε Approve τώρα, στείλε το email
        if ($old_status === 'pending' && $new_status === 'approved') {
            $user_info = get_userdata($user_id);
            $to = $user_info->user_email;
            $subject = __( 'Ο λογαριασμός σας εγκρίθηκε!', 'ruined' );
            $message = sprintf( __( 'Γεια σας, ο λογαριασμός σας στο %s εγκρίθηκε επιτυχώς. Μπορείτε πλέον να συνδεθείτε χρησιμοποιώντας τα στοιχεία σας.', 'ruined' ), get_bloginfo('name') );

            wp_mail($to, $subject, $message);
        }
    }

    // Save Company Name
    if (isset($_POST['company_name'])) {
        update_user_meta($user_id, 'company_name', sanitize_text_field($_POST['company_name']));
    }

    // Save Municipality Name
    if (isset($_POST['municipality_name'])) {
        update_user_meta($user_id, 'municipality_name', sanitize_text_field($_POST['municipality_name']));
    }
}