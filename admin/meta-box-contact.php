<?php
/**
 * Meta box contact
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_id = get_post_meta( $post->ID, '_pronamic_moneybird_contact_id', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_contact_id"><?php esc_html_e( 'Contact ID', 'pronamic-moneybird' ); ?></label>
		</th>
		<td>
			<input id="pronamic_moneybird_contact_id" name="_pronamic_moneybird_contact_id" value="<?php echo esc_attr( $contact_id ); ?>" type="text" class="regular-text code" />
		</td>
	</tr>
</table>
