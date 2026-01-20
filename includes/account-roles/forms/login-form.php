<?php
if ( ! defined('ABSPATH') ) exit;

add_action( 'sigma_auth_login_form_inside_modal', 'sigma_auth_login_form' );

function sigma_auth_login_form() {
	?>
	<form class="woocommerce-form woocommerce-form-login js-ajax-login-form"
      method="post"
      action="#"
      aria-labelledby="login-form-title"
      aria-describedby="login-form-description">

		<h2 id="login-form-title" class="screen-reader-text"><?php esc_html_e( 'Σύνδεση', 'ruined' ); ?></h2>
		<p id="login-form-description" class="screen-reader-text"><?php esc_html_e( 'Συμπληρώστε τα στοιχεία σύνδεσης', 'ruined' ); ?></p>

		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<input type="hidden" name="nonce"
		       value="<?php echo wp_create_nonce( 'sigma-login' ); ?>">

		<!-- EMAIL με floating label -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field">
			<input type="text"
			       class="woocommerce-Input woocommerce-Input--text input-text"
			       name="username"
			       id="username"
			       autocomplete="username"
			       placeholder=" "
			       value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
			       required
			       aria-required="true"
			       aria-invalid="false"
			       aria-describedby="username-description"
			       data-rule-email="true"
			       data-msg-email="<?php esc_attr_e( 'Παρακαλώ εισάγετε μια έγκυρη διεύθυνση email.', 'ruined' ); ?>"
			       data-rule-required="<?php esc_attr_e( 'Το πεδίο email απαιτείται.', 'ruined' ); ?>" />
			    <span id="username-description" class="screen-reader-text"><?php esc_html_e( 'Παρακαλώ εισάγετε τη διεύθυνση email σας', 'ruined' ); ?></span>
			<label for="username">
				<?php esc_html_e( 'Λογαριασμός Email', 'ruined' ); ?>
			</label>
		</p>

		<!-- PASSWORD με floating label (και optional eye icon) -->
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide floating-field floating-field--password">
			<input class="woocommerce-Input woocommerce-Input--text input-text"
			       type="password"
			       name="password"
			       id="login_password"
			       autocomplete="current-password"
			       placeholder=" "
			       required
			       aria-required="true"
			       aria-invalid="false"
			       aria-describedby="password-description"
			       data-rule-required="<?php esc_attr_e( 'Το πεδίο κωδικού απαιτείται.', 'ruined' ); ?>" />
			    <span id="password-description" class="screen-reader-text"><?php esc_html_e( 'Παρακαλώ εισάγετε τον κωδικό σας', 'ruined' ); ?></span>
			<label for="password">
				<?php esc_html_e( 'Κωδικός Πρόσβασης', 'ruined' ); ?>
			</label>

			<button type="button"
			        class="floating-field__toggle js-password-toggle"
			        aria-label="<?php esc_attr_e( 'Εμφάνιση κωδικού', 'ruined' ); ?>"
			        aria-controls="login_password"
			        aria-pressed="false">

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


		<?php
		// extra hooks της φόρμας login
		do_action( 'woocommerce_login_form' );
		?>

		<p class="form-row submit_btn_row">
			<input type="hidden" name="rememberme" value="forever"/>
			<?php
			rv_button_arrow( [
				'text'     => __( 'Σύνδεση στο Λογαριασμό', 'ruined' ),
				'url'      => '#',
				'target'   => '_self',
				'variant'  => 'black',
				'class'    => 'sigma-auth-submit sigma-auth-submit--login',
				'register' => false,
			] );
			?>
		</p>


		<?php do_action( 'woocommerce_login_form_end' ); ?>

	</form>
	<?php
}
