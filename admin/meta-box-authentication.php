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

$api_token = \get_post_meta( $post->ID, '_pronamic_moneybird_api_token', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_api_token"><?php \esc_html_e( 'API token', 'orbis-tasks' ); ?></label>
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
