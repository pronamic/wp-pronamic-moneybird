<?php
/**
 * Meta box post
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pm_post_type = get_post_type( $post );

$fields = [
	'pronamic_moneybird_contact_id'           => [
		'key'     => '_pronamic_moneybird_contact_id',
		'value'   => get_post_meta( $post->ID, '_pronamic_moneybird_contact_id', true ),
		'label'   => __( 'Contact ID', 'pronamic-moneybird' ),
		'feature' => 'pronamic_moneybird_contact',
	],
	'pronamic_moneybird_financial_account_id' => [
		'key'     => '_pronamic_moneybird_financial_account_id',
		'value'   => get_post_meta( $post->ID, '_pronamic_moneybird_financial_account_id', true ),
		'label'   => __( 'Financial account ID', 'pronamic-moneybird' ),
		'feature' => 'pronamic_moneybird_financial_account',
	],
	'pronamic_moneybird_ledger_account_id'    => [
		'key'     => '_pronamic_moneybird_ledger_account_id',
		'value'   => get_post_meta( $post->ID, '_pronamic_moneybird_ledger_account_id', true ),
		'label'   => __( 'Ledger account ID', 'pronamic-moneybird' ),
		'feature' => 'pronamic_moneybird_ledger_account',
	],
	'pronamic_moneybird_product_id'           => [
		'key'     => '_pronamic_moneybird_product_id',
		'value'   => get_post_meta( $post->ID, '_pronamic_moneybird_product_id', true ),
		'label'   => __( 'Product ID', 'pronamic-moneybird' ),
		'feature' => 'pronamic_moneybird_product',
	],
	'pronamic_moneybird_project_id'           => [
		'key'     => '_pronamic_moneybird_project_id',
		'value'   => get_post_meta( $post->ID, '_pronamic_moneybird_project_id', true ),
		'label'   => __( 'Project ID', 'pronamic-moneybird' ),
		'feature' => 'pronamic_moneybird_project',
	],
];

$fields = array_filter(
	$fields,
	function ( $field ) use ( $pm_post_type ) {
		return post_type_supports( $pm_post_type, $field['feature'] );
	}
);

?>
<table class="form-table">

	<?php foreach ( $fields as $field_id => $field ) : ?>

		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			</th>
			<td>
				<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" type="text" class="regular-text code" />
			</td>
		</tr>

	<?php endforeach; ?>

</table>
