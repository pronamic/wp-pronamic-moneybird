<?php
/**
 * New sales invoice template
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Orbis\Tasks
 */

namespace Pronamic\Moneybird;

use DateTimeImmutable;

$authorization_id  = \get_option( 'pronamic_moneybird_authorization_post_id', '' );
$administration_id = '';

if ( '' !== $authorization_id ) {
	$administration_id = \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );
}

$moneybird_errors = \apply_filters( 'pronamic_moneybird_errors', [] );

$sales_invoice = new SalesInvoice();

\do_action( 'pronamic_moneybird_new_sales_invoice', $sales_invoice );

$sales_invoice->details_attributes[] = new SalesInvoiceDetail();
$sales_invoice->details_attributes[] = new SalesInvoiceDetail();
$sales_invoice->details_attributes[] = new SalesInvoiceDetail();
$sales_invoice->details_attributes[] = new SalesInvoiceDetail();
$sales_invoice->details_attributes[] = new SalesInvoiceDetail();

\get_header();

?>
<div>
	<h2><?php \esc_html_e( 'Moneybird', 'pronamic-moneybird' ); ?></h2>

	<h3><?php \esc_html_e( 'Add invoice', 'pronamic-moneybird' ); ?></h3>

	<?php if ( \count( $moneybird_errors ) > 0 ) : ?>

		<div class="alert alert-warning" role="alert">
			<ul class="m-0">
				<?php foreach ( $moneybird_errors as $moneybird_error ) : ?>
					<li>
						<?php echo \esc_html( $moneybird_error->get_error_message() ); ?>

						<?php if ( current_user_can( 'manage_options' ) ) : ?>

							<pre><?php echo \esc_html( wp_json_encode( $moneybird_error->get_error_data(), \JSON_PRETTY_PRINT ) ); ?></pre>

						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	<?php endif; ?>

	<form method="post" action="">
		<div class="card">
			<div class="card-header">
				<?php \esc_html_e( 'Base', 'pronamic-moneybird' ); ?>
			</div>

			<div class="card-body">
				<div class="mb-3">
					<label for="pronamic_moneybird_authorization_id" class="form-label"><?php \esc_html_e( 'Authorization', 'pronamic-moneybird' ); ?></label>
					<input id="pronamic_moneybird_authorization_id" name="authorization_id" value="<?php echo \esc_attr( $authorization_id ); ?>" type="text" class="form-control" required>
				</div>

				<div class="mb-3">
					<label for="pronamic_moneybird_administration_id" class="form-label"><?php \esc_html_e( 'Administration', 'pronamic-moneybird' ); ?></label>
					<input id="pronamic_moneybird_administration_id" name="administration_id" value="<?php echo \esc_attr( $administration_id ); ?>" type="text" class="form-control" required>
				</div>

				<div class="mb-3">
					<label for="pronamic_moneybird_contact_id" class="form-label"><?php \esc_html_e( 'Contact', 'pronamic-moneybird' ); ?></label>
					<input id="pronamic_moneybird_contact_id" name="sales_invoice[contact_id]" value="<?php echo \esc_attr( $sales_invoice->contact_id ); ?>" type="text" class="form-control" required>
				</div>
			</div>
		</div>

		<div class="card mt-4">
			<div class="card-header">
				<?php \esc_html_e( 'Lines', 'pronamic-moneybird' ); ?>
			</div>

			<div class="card-body">

				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col"><?php \esc_html_e( 'Number', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Description', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Amount', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Product ID', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Period', 'pronamic-moneybird' ); ?></th>
						</tr>
					</thead>

					<tbody>

						<?php foreach ( $sales_invoice->details_attributes as $i => $detail ) : ?>

							<tr>
								<?php

								$name = \sprintf(
									'sales_invoice[details_attributes][%d]',
									$i
								);

								?>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="number" class="form-control" />',
										\esc_attr( $name . '[amount]' ),
										\esc_attr( $detail->amount ?? '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="text" class="form-control" maxlength="36" />',
										\esc_attr( $name . '[description]' ),
										\esc_attr( $detail->description ?? '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="number" step="0.01" class="form-control" />',
										\esc_attr( $name . '[price]' ),
										\esc_attr( $detail->price ?? '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="text" class="form-control" />',
										\esc_attr( $name . '[product_id]' ),
										\esc_attr( $detail->product_id ?? '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="text" class="form-control" />',
										\esc_attr( $name . '[period]' ),
										\esc_attr( $detail->period ?? '' )
									);

									?>
								</td>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>

			</div>
		</div>

		<div class="mt-4">
			<?php

			\wp_nonce_field( 'pronamic_moneybird_create_sales_invoice', 'pronamic_moneybird_nonce' );

			\printf(
				'<button name="pronamic_moneybird_create_sales_invoice" value="true" type="submit" class="btn btn-primary">%s</button>',
				\esc_html__( 'Create invoice', 'pronamic-moneybird' )
			); 

			?>
		</div>
	</form>
</div>
<?php

\get_footer();
