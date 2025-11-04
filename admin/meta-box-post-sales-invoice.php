<?php
/**
 * Meta box post sales invoice
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2025 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$json = \get_post_meta( $post->ID, '_pronamic_moneybird_sales_invoice', true );

$data = \json_decode( $json );

$sales_invoice = new SalesInvoice();

$detail_properties = [
	'amount',
	'description',
	'price',
	'product_id',
	'project_id',
];

if ( \is_object( $data ) ) {
	if ( \property_exists( $data, 'details_attributes' ) && \is_array( $data->details_attributes ) ) {
		foreach ( $data->details_attributes as $detail_data ) {
			$detail = new SalesInvoiceDetail();

			foreach ( $detail_properties as $property ) {
				if ( \property_exists( $detail_data, $property ) ) {
					$detail->$property = $detail_data->$property;
				}
			}

			$sales_invoice->details_attributes[] = $detail;
		}
	}
}

$sales_invoice->details_attributes[] = new SalesInvoiceDetail();

?>

<table>
	<thead>
		<tr>
			<th scope="col"><?php \esc_html( \_x( 'Amount', 'quantity', 'pronamic-moneybird' ) ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Description', 'pronamic-moneybird' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Price', 'pronamic-moneybird' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Product ID', 'pronamic-moneybird' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Project ID', 'pronamic-moneybird' ); ?></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach ( $sales_invoice->details_attributes as $i => $detail ) : ?>

			<tr>
				<?php

				$name = \sprintf(
					'_pronamic_moneybird_sales_invoice[details_attributes][%d]',
					$i
				);

				?>
				<td>
					<?php

					\printf(
						'<input name="%s" value="%s" type="text" class="form-control" />',
						\esc_attr( $name . '[amount]' ),
						\esc_attr( $detail->amount ?? '' )
					);

					?>
				</td>
				<td>
					<?php

					\printf(
						'<textarea name="%s" cols="20" rows="3" class="form-control">%s</textarea>',
						\esc_attr( $name . '[description]' ),
						\esc_textarea( $detail->description ?? '' )
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
						\esc_attr( $name . '[project_id]' ),
						\esc_attr( $detail->project_id ?? '' )
					);

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>
