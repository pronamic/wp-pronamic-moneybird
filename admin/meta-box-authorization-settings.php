<?php
/**
 * Meta box authorization settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$api_token         = get_post_meta( $post->ID, '_pronamic_moneybird_api_token', true );
$administration_id = get_post_meta( $post->ID, '_pronamic_moneybird_administration_id', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_api_token"><?php esc_html_e( 'API token', 'pronamic-moneybird' ); ?></label>
		</th>
		<td>
			<input id="pronamic_moneybird_api_token" name="_pronamic_moneybird_api_token" value="<?php echo esc_attr( $api_token ); ?>" type="text" class="regular-text code" />

			<p class="description">
				<?php

				$url = 'https://moneybird.com/user/applications/new';

				echo wp_kses(
					sprintf(
						/* translators: %s: Moneybird URL. */
						__( 'You can create an API token via %s.', 'pronamic-moneybird' ),
						sprintf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( $url ),
							esc_html( $url )
						)
					),
					[
						'a' => [
							'href'   => true,
							'target' => true,
						],
					]
				);

				?>
			</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="pronamic_moneybird_administration_id"><?php esc_html_e( 'Default administration', 'pronamic-moneybird' ); ?></label>
		</th>
		<td>
			<input id="pronamic_moneybird_administration_id" name="_pronamic_moneybird_administration_id" value="<?php echo esc_attr( $administration_id ); ?>" type="text" class="regular-text code" />

			<p class="description">
				<?php esc_html_e( 'This administration ID is used by default for API requests.', 'pronamic-moneybird' ); ?>				
			</p>
		</td>
	</tr>
</table>
