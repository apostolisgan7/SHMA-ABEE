<?php
/**
 * Catalog Menu Popup
 *
 * @package Ruined
 */
?>
<div class="catalog-overlay" data-catalog-overlay></div>

<div class="catalog-menu" data-catalog-panel>
	<div class="catalog-menu__header">
		<?php if ( has_custom_logo() ) : the_custom_logo(); endif; ?>
		<h2 class="catalog-menu__title"><?php _e( 'Κατάλογος Προϊόντων', 'ruined' ); ?></h2>
		<button class="catalog-menu__close" type="button" data-catalog-close aria-label="<?php esc_attr_e('Close', 'ruined'); ?>">
			×
		</button>
	</div>

	<div class="catalog-menu__body">
		<!-- Left column: κεντρικές κατηγορίες -->
		<div class="catalog-menu__left">
			<h3 class="catalog-menu__subtitle"><?php _e( 'Κεντρικές Κατηγορίες', 'ruined' ); ?></h3>
			<?php
			wp_nav_menu([
				'theme_location' => 'catalog-menu',
				'container'      => false,
				'menu_class'     => 'catalog-menu__list',
				'fallback_cb'    => false,
			]);
			?>
			<button class="catalog-menu__all btn-icon">
				<span><?php _e( 'Όλες οι κατηγορίες', 'ruined' ); ?></span>
			</button>
		</div>

		<!-- Right column: δεύτερη στήλη / δυναμικό περιεχόμενο -->
		<div class="catalog-menu__right">
			<h3 class="catalog-menu__subtitle"><?php _e( 'Πινακίδες', 'ruined' ); ?></h3>
			<ul class="catalog-menu__sublist">
				<li>Πινακίδες Σήμανσης Κ.Ο.Κ.</li>
				<li>Υλικά Οδικής Ασφάλειας</li>
				<li>Είδη Σήμανσης-Ασφάλειας Για Δήμους</li>
				<li>Υλικά Οδοστρώματος</li>
				<li class="is-active">Παρακάμψεις</li>
			</ul>

			<button class="catalog-menu__all btn-icon">
				<span><?php _e( 'Όλα τα προϊόντα', 'ruined' ); ?></span>
			</button>
		</div>
	</div>

	<div class="catalog-menu__footer">
		<a href="#"><?php _e( 'Ο Λογαριασμός μου', 'ruined' ); ?></a>
		<a href="#"><?php _e( 'Αγαπημένα προϊόντα', 'ruined' ); ?></a>
		<a href="#"><?php _e( 'Επικοινωνία με πωλήσεις', 'ruined' ); ?></a>
	</div>
</div>
