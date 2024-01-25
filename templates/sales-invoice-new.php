<?php
/**
 * New sales invoice template
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Orbis\Tasks
 */

get_header();

?>
<div>
	<h2><?php esc_html_e( 'Moneybird', 'pronamic-moneybird' ); ?></h2>

	<h3><?php esc_html_e( 'Add invoice', 'pronamic-moneybird' ); ?></h3>

	<form method="post" action="">
		<div class="mt-4">
			<?php

			wp_nonce_field( 'pronamic_moneybird_create_sales_invoice', 'pronamic_moneybird_nonce' );

			printf(
				'<button name="pronamic_moneybird_create_sales_invoice" value="true" type="submit" class="btn btn-primary">%s</button>',
				__( 'Create invoice', 'pronamic-moneybird' )
			); 

			?>
		</div>
	</form>
</div>
<?php

get_footer();
