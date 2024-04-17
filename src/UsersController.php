<?php
/**
 * Users controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use WP_CLI;
use WC_Order;
use WC_Product;
use WC_Product_Subscription;
use WC_Order_Item_Product;
use WP_Post;

/**
 * Users controller class
 */
final class UsersController {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_filter( 'manage_users_columns', [ $this, 'manage_users_columns' ] );

		\add_filter( 'manage_users_custom_column', [ $this, 'manage_users_custom_column' ], 10, 3 );
	}

	/**
	 * Manage users columns.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
	 * @param string[] $columns Columns.
	 * @return string[]
	 */
	public function manage_users_columns( $columns ) {
		$columns['pronamic_moneybird'] = \__( 'Moneybird', 'pronamic-moneybird' );

		$new_columns = [];

		foreach ( $columns as $name => $label ) {
			if ( \in_array( $name, [ 'role', 'posts' ], true ) ) {
				$new_columns['pronamic_moneybird'] = $columns['pronamic_moneybird'];
			}

			$new_columns[ $name ] = $label;
		}

		$columns = $new_columns;

		return $columns;
	}

	/**
	 * Manage users custom column.
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/manage_users_custom_column/
	 * @param string $output      Output.
	 * @param string $column_name Column name.
	 * @param int    $user_id     User ID.
	 * @return string
	 */
	public function manage_users_custom_column( $output, $column_name, $user_id ) {
		if ( 'pronamic_moneybird' !== $column_name ) {
			return $output;
		}

		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$contact_id = \get_user_meta( $user_id, '_pronamic_moneybird_contact_id', true );

		if ( '' !== $contact_id ) {
			$output .= \sprintf(
				'<a href="%s" title="%s"><span class="dashicons dashicons-businessperson"></span></a>',
				\esc_url(
					Contact::get_remote_link_by_id(
						$administration_id,
						$contact_id
					)
				),
				\esc_attr(
					\sprintf(
						/* translators: %s: Contact ID. */
						__( 'Contact ID: %s', 'pronamic-moneybird' ),
						$contact_id
					)
				)
			);
		}

		return $output;
	}
}
