<?php
/**
 * Auth Modal (Login / Signup)
 * Template Part
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="sigma-auth-overlay" class="sigma-auth-overlay" aria-hidden="true">
    <div class="sigma-auth-backdrop js-auth-close"></div>

    <div id="sigma-auth-modal" class="sigma-auth-modal" data-auth-mode="login" data-auth-role="individual">

        <!-- Close -->
        <button type="button" class="sigma-auth-close js-auth-close"
                aria-label="<?php esc_attr_e('Κλείσιμο', 'ruined'); ?>">
            ×
        </button>

        <!-- Header -->
        <div class="sigma-auth-header">
            <div class="sigma-auth-logo-wrap">
                <?= get_custom_logo(); ?>
                <div class="sigma-auth-logo-text">
                </div>

            </div>

            <h2 class="sigma-auth-title sigma-auth-title--login">
                <?php _e('Σύνδεση Λογαριασμού', 'ruined'); ?>
            </h2>
            <h2 class="sigma-auth-title sigma-auth-title--signup">
                <?php _e('Δημιουργία Λογαριασμού', 'ruined'); ?>
            </h2>

            <p class="sigma-auth-subtitle">
                <?php _e('Καλύπτουμε πλήρως όλες τις ανάγκες του κλάδου, προσφέροντας ολοκληρωμένες λύσεις.', 'ruined'); ?>
            </p>
        </div>

        <!-- Role tabs -->
        <div class="sigma-auth-roles">
            <div class="sigma-auth-roles-inner">
                <div class="sigma-auth-roles-pill"></div>
                <button type="button" class="sigma-auth-role-btn is-active" data-role="individual">
                    <?php _e('Ιδιώτες', 'ruined'); ?>
                </button>
                <button type="button" class="sigma-auth-role-btn" data-role="company">
                    <?php _e('Εταιρείες', 'ruined'); ?>
                </button>
                <button type="button" class="sigma-auth-role-btn" data-role="municipality">
                    <?php _e('Δήμοι', 'ruined'); ?>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="sigma-auth-body">



            <div class="sigma-auth-pane sigma-auth-pane--login is-active">
                <?php
                do_action('sigma_auth_login_form_inside_modal');
                ?>
            </div>

            <div class="sigma-auth-pane sigma-auth-pane--signup">
                <?php
                do_action('sigma_auth_register_form_inside_modal');
                ?>
            </div>

        </div>

        <!-- Footer -->
        <div class="sigma-auth-footer">
            <div class="sigma-auth-footer-left">
                <a class="sigma-auth-toggle js-auth-toggle">
                    <span class="sigma-auth-toggle-login">
                        <?php _e('Δημιουργία Λογαριασμού', 'ruined'); ?>
                    </span>
                    <span class="sigma-auth-toggle-signup">
                        <?php _e('Έχετε ήδη λογαριασμό; Σύνδεση', 'ruined'); ?>
                    </span>
                </a>

                <a class="sigma-auth-forgot js-auth-forgot"
                   href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
                    <?php _e('Ξεχάσατε το κωδικό σας;', 'ruined'); ?>
                </a>

            </div>

            <span class="sigma-auth-footer-brand">
               σημα αββε 2025
            </span>
        </div>

    </div>
</div>


<script>
    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('.js-password-toggle');
        if (!toggle) return;

        const wrapper = toggle.closest('.floating-field--password');
        if (!wrapper) return;

        const input = wrapper.querySelector('input');
        if (!input) return;

        e.preventDefault();

        if (input.type === 'password') {
            input.type = 'text';
            toggle.classList.add('is-visible');
        } else {
            input.type = 'password';
            toggle.classList.remove('is-visible');
        }
    });

</script>
