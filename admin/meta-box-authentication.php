<?php
/**
 * Meta box authentication
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Orbis\Tasks
 */

namespace Pronamic\Moneybird;

use Pronamic\WordPress\Http\Facades\Http;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

$api_token         = \get_post_meta( $post->ID, '_pronamic_moneybird_api_token', true );
$administration_id = \get_post_meta( $post->ID, '_pronamic_moneybird_administration_id', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_api_token"><?php \esc_html_e( 'API token', 'pronamic-moneybird' ); ?></label>
		</th>
		<td>
			<code><?php echo \esc_html( $api_token ); ?></code>
		</td>
	</tr>
</table>

<?php

$response = Http::get(
	'https://moneybird.com/api/v2/administrations.json',
	[
		'headers' => [
			'Authorization' => 'Bearer ' . $api_token,
		],
	]
);

$data = $response->json();

echo '<pre>';
var_dump( $data );
echo '</pre>';

$data = [
	'sales_invoice' => [
		'reference'          => null,
		'contact_id'         => '410289412558030139',
		'details_attributes' => [
			[
				'description' => 'Rocking Chair',
				'price'       => 129.95,
			],
		],
	],
];

if ( false ) {
	$response = Http::post(
		\strtr(
			'https://moneybird.com/api/:version/:administration_id/:resource_path.:format',
			[
				':version'           => 'v2',
				':administration_id' => $administration_id,
				':resource_path'     => 'sales_invoices',
				':format'            => 'json',
			]
		),
		[
			'headers' => [
				'Authorization' => 'Bearer ' . $api_token,
				'Content-Type'  => 'application/json',
			],
			'body'    => \wp_json_encode( $data ),
		]
	);

	$data = $response->json();

	echo '<pre>';
	var_dump( $data );
	echo '</pre>';
}
