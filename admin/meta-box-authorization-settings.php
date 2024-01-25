<?php
/**
 * Meta box authorization settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Orbis\Tasks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$api_token = get_post_meta( $post->ID, '_pronamic_moneybird_api_token', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_api_token"><?php esc_html_e( 'API token', 'orbis-tasks' ); ?></label>
		</th>
		<td>
			<input id="pronamic_moneybird_api_token" name="_pronamic_moneybird_api_token"value="<?php echo esc_attr( $api_token ); ?>" type="text" class="regular-text code" />
		</td>
	</tr>
</table>
