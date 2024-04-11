<?php

$tax_rates = get_option( 'pronamic_moneybird_woocommerce_tax_rates' );
$tax_rates = is_array( $tax_rates ) ? $tax_rates : [];

/*
 * WooCommerce settings tax.
 * @link https://github.com/woocommerce/woocommerce/blob/3.5.3/includes/admin/settings/class-wc-settings-tax.php#L54-L73
 *
 * `get_tax_classes` » https://github.com/woocommerce/woocommerce/blob/3.5.3/includes/class-wc-tax.php#L698-L705
 */
$tax_classes = WC_Tax::get_tax_classes();

array_unshift( $tax_classes, '' );

?>
<style type="text/css">
	.form-table .widefat th,
	.form-table .widefat td {
		padding: 8px 10px;
	}
</style>

<table class="widefat">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Class', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Country', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'State', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Rate', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Name', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Priority', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Compound', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Shipping', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Order', 'pronamic-moneybird' ); ?></th>
			<th><?php esc_html_e( 'Moneybird tax rate ID', 'pronamic-moneybird' ); ?></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach ( $tax_classes as $tax_class ) : ?>

			<?php foreach ( WC_Tax::get_rates_for_tax_class( $tax_class ) as $rate ) : ?>

				<tr>
					<td>
						<?php

						$name = $tax_class;

						if ( empty( $name ) ) {
							$name = __( 'Standard rate', 'pronamic-moneybird' );
						}

						echo esc_html( $name );

						?>
					</td>
					<td>
						<?php echo esc_html( empty( $rate->tax_rate_country ) ? '*' : $rate->tax_rate_country ); ?>
					</td>
					<td>
						<?php echo esc_html( empty( $rate->tax_rate_state ) ? '*' : $rate->tax_rate_state ); ?>
					</td>
					<td>
						<?php echo esc_html( WC_Tax::get_rate_percent( $rate ) ); ?>
					</td>
					<td>
						<?php echo esc_html( WC_Tax::get_rate_label( $rate ) ); ?>
					</td>
					<td>
						<?php echo esc_html( $rate->tax_rate_priority ); ?>
					</td>
					<td>
						<?php echo esc_html( WC_Tax::is_compound( $rate ) ? 1 : 0 ); ?>
					</td>
					<td>
						<?php echo esc_html( $rate->tax_rate_shipping ); ?>
					</td>
					<td>
						<?php echo esc_html( $rate->tax_rate_order ); ?>
					</td>
					<td>
						<?php

						$tax_rate_id = null;

						if ( isset( $tax_rates[ $rate->tax_rate_id ] ) ) {
							$tax_rate_id = $tax_rates[ $rate->tax_rate_id ];
						}

						$name = sprintf(
							'pronamic_moneybird_woocommerce_tax_rates[%s]',
							$rate->tax_rate_id
						);

						printf(
							'<input type="text" value="%s" name="%s" />',
							esc_attr( $tax_rate_id ),
							esc_attr( $name )
						);

						?>
					</td>
				</tr>

			<?php endforeach; ?>

		<?php endforeach; ?>

	</tbody>
</table>
