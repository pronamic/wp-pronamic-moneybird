<?php
/**
 * Page dashboard
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'pronamic_moneybird' ); ?>

		<?php do_settings_sections( 'pronamic_moneybird' ); ?>

		<?php submit_button(); ?>
	</form>
</div>
