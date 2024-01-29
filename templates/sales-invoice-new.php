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

$authorization_id  = \get_option( 'pronamic_moneybird_authorization_post_id', '' );
$administration_id = '';

if ( '' !== $authorization_id ) {
	$administration_id = \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );
}

$lines = \range( 1, 5 );

$moneybird_errors = \apply_filters( 'pronamic_moneybird_errors', [] );

$sales_invoice = new SalesInvoice();

\add_action(
	'pronamic_moneybird_new_sales_invoice',
	function ( $sales_invoice ) {
		global $wpdb;

		if ( ! \array_key_exists( 'orbis_project_id', $_GET ) ) {
			return;
		}

		$project_id = \sanitize_text_field( \wp_unslash( $_GET['orbis_project_id'] ) );

		$where = '1 = 1';

		$where .= $wpdb->prepare( ' AND project.id = %d', $project_id );

		$query = "
			SELECT
				project.id AS project_id,
				project.name AS project_name,
				project.billable_amount AS project_billable_amount,
				project.number_seconds AS project_billable_time,
				project.invoice_number AS project_invoice_number,
				project.post_id AS project_post_id,
				project.start_date AS project_start_date,
				manager.ID AS project_manager_id,
				manager.display_name AS project_manager_name,
				principal.id AS principal_id,
				principal.name AS principal_name,
				principal.post_id AS principal_post_id,
				project_invoice_totals.project_billed_time,
				project_invoice_totals.project_billed_amount,
				project_invoice_totals.project_invoice_numbers,
				project_timesheet_totals.project_timesheet_time
			FROM
				$wpdb->orbis_projects AS project
					INNER JOIN
				wp_posts AS project_post
						ON project.post_id = project_post.ID
					INNER JOIN
				wp_users AS manager
						ON project_post.post_author = manager.ID
					INNER JOIN
				$wpdb->orbis_companies AS principal
						ON project.principal_id = principal.id
					LEFT JOIN
				(
					SELECT
						project_invoice.project_id,
						SUM( project_invoice.seconds ) AS project_billed_time,
						SUM( project_invoice.amount ) AS project_billed_amount,
						GROUP_CONCAT( DISTINCT project_invoice.invoice_number ) AS project_invoice_numbers
					FROM
						$wpdb->orbis_projects_invoices AS project_invoice
					GROUP BY
						project_invoice.project_id
				) AS project_invoice_totals ON project_invoice_totals.project_id = project.id
					LEFT JOIN
				(
					SELECT
						project_timesheet.project_id,
						SUM( project_timesheet.number_seconds ) AS project_timesheet_time
					FROM
						$wpdb->orbis_timesheets AS project_timesheet
					GROUP BY
						project_timesheet.project_id
				) AS project_timesheet_totals ON project_timesheet_totals.project_id = project.id
			WHERE
				$where
			GROUP BY
				project.id
			ORDER BY
				principal.name
			;
		";

		$project = $wpdb->get_row( $query );

		echo '<pre>';
		\var_dump( $project );
		echo '</pre>';
		exit;
	}
);

\do_action( 'pronamic_moneybird_new_sales_invoice', $sales_invoice );

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
							<th scope="col"><?php \esc_html_e( 'Product', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Description', 'pronamic-moneybird' ); ?></th>
							<th scope="col"><?php \esc_html_e( 'Amount', 'pronamic-moneybird' ); ?></th>
						</tr>
					</thead>

					<tbody>

						<?php foreach ( $lines as $i => $line ) : ?>

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
										\esc_attr( '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="text" class="form-control" />',
										\esc_attr( $name . '[product_id]' ),
										\esc_attr( '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="text" class="form-control" maxlength="36" />',
										\esc_attr( $name . '[description]' ),
										\esc_attr( '' )
									);

									?>
								</td>
								<td>
									<?php

									\printf(
										'<input name="%s" value="%s" type="number" step="0.01" class="form-control" />',
										\esc_attr( $name . '[price]' ),
										\esc_attr( '' )
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
