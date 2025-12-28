<?php
if ( ! defined('ABSPATH') ) exit;

add_action( 'sigma_auth_register_form_inside_modal', 'sigma_auth_register_form' );

function sigma_auth_register_form() {
	?>
	<form method="post"
	      class="woocommerce-form woocommerce-form-register register js-ajax-register-form">

		<?php do_action( 'woocommerce_register_form_start' ); ?>

		<input type="hidden"
		       name="nonce"
		       value="<?php echo wp_create_nonce( 'sigma-register' ); ?>">

		<!-- hidden role (αλλάζει από JS) -->
		<input type="hidden"
		       name="sigma_customer_type"
		       class="js-auth-role-input"
		       value="customer_b2c">

		<!-- ================= EMAIL ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field">
			<input type="email"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="email"
			       id="reg_email"
			       autocomplete="email"
			       placeholder=" "
			       required>
			<label for="reg_email"><?php esc_html_e( 'Email', 'ruined' ); ?></label>
		</p>

		<!-- ================= PASSWORD ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field floating-field--password">
			<input class="woocommerce-Input woocommerce-Input--text input-text"
			       type="password"
			       name="password"
			       id="register_password"
			       autocomplete="current-password"
			       placeholder=" "
			       required>
			<label for="register_password"><?php esc_html_e( 'Κωδικός Πρόσβασης', 'ruined' ); ?></label>

			<button type="button"
			        class="floating-field__toggle js-password-toggle"
			        aria-label="<?php esc_attr_e( 'Εμφάνιση κωδικού', 'ruined' ); ?>">

				<!-- OPEN eye -->
				<span class="icon-eye icon-eye--open">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 5C5.63636 5 2 12 2 12C2 12 5.63636 19 12 19C18.3636 19 22 12 22 12C22 12 18.3636 5 12 5Z"
                      stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                      stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>

				<!-- CLOSED eye -->
				<span class="icon-eye icon-eye--closed">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20 14.8335C21.3082 13.3317 22 12 22 12C22 12 18.3636 5 12 5C11.6588 5 11.3254 5.02013 11 5.05822C10.6578 5.09828 10.3244 5.15822 10 5.23552M12 9C12.3506 9 12.6872 9.06015 13 9.17071C13.8524 9.47199 14.528 10.1476 14.8293 11C14.9398 11.3128 15 11.6494 15 12M3 3L21 21M12 15C11.6494 15 11.3128 14.9398 11 14.8293C10.1476 14.528 9.47198 13.8524 9.1707 13C9.11386 12.8392 9.07034 12.6721 9.04147 12.5M4.14701 9C3.83877 9.34451 3.56234 9.68241 3.31864 10C2.45286 11.1282 2 12 2 12C2 12 5.63636 19 12 19C12.3412 19 12.6746 18.9799 13 18.9418"
                      stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>

			</button>
		</p>

		<!-- ================= ΟΝΟΜΑ (ΙΔΙΩΤΗΣ) ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field js-customer-name-field">
			<input type="text"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="customer_name"
			       id="customer_name"
			       placeholder=" ">
			<label for="customer_name"><?php esc_html_e( 'Ονοματεπώνυμο', 'ruined' ); ?></label>
		</p>

		<!-- ================= ΕΠΩΝΥΜΙΑ ΕΤΑΙΡΕΙΑΣ ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field js-company-name-field"
		   style="display:none;">
			<input type="text"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="company_name"
			       id="company_name"
			       placeholder=" ">
			<label for="company_name"><?php esc_html_e( 'Επωνυμία Εταιρείας', 'ruined' ); ?></label>
		</p>

		<!-- ================= ΔΗΜΟΣ ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field js-municipality-name-field"
		   style="display:none;">
			<input type="text"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="municipality_name"
			       id="municipality_name"
			       placeholder=" ">
			<label for="municipality_name"><?php esc_html_e( 'Όνομα Δήμου', 'ruined' ); ?></label>
		</p>

		<!-- ================= ΤΗΛΕΦΩΝΟ (ΟΛΟΙ) ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field">
			<input type="tel"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="phone"
			       id="phone"
			       placeholder=" "
			       required>
			<label for="phone"><?php esc_html_e( 'Τηλέφωνο', 'ruined' ); ?></label>
		</p>

		<!-- ================= ΑΦΜ (ΕΤΑΙΡΕΙΑ / ΔΗΜΟΣ) ================= -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field js-vat-field"
		   style="display:none;">
			<input type="text"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="vat"
			       id="vat"
			       placeholder=" ">
			<label for="vat"><?php esc_html_e( 'ΑΦΜ', 'ruined' ); ?></label>
		</p>

		<?php do_action( 'woocommerce_register_form' ); ?>

		<p class="woocommerce-form-row form-row">
			<?php
			rv_button_arrow( [
				'text'    => __( 'Ολοκλήρωση Εγγραφής', 'ruined' ),
				'variant' => 'black',
				'class'   => 'sigma-auth-submit sigma-auth-submit--register',
			] );
			?>
		</p>

		<?php do_action( 'woocommerce_register_form_end' ); ?>

	</form>
	<?php
}
