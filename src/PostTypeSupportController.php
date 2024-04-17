<?php
/**
 * Post type support controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Post type support controller class
 */
final class PostTypeSupportController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		\add_filter( 'manage_posts_columns', [ $this, 'manage_posts_columns' ], 10, 2 );
		\add_action( 'manage_posts_custom_column', [ $this, 'manage_posts_custom_column' ], 10, 2 );

		\add_action( 'save_post', [ $this, 'save_post' ] );
	}

	/**
	 * Check if post types supports one of the Moneybird features.
	 * 
	 * @param string $post_type Post type.
	 * @return bool
	 */
	private function post_type_supports_moneybird( $post_type ) {
		if ( \post_type_supports( $post_type, 'pronamic_moneybird_contact' ) ) {
			return true;
		}

		if ( \post_type_supports( $post_type, 'pronamic_moneybird_ledger_account' ) ) {
			return true;
		}

		if ( \post_type_supports( $post_type, 'pronamic_moneybird_product' ) ) {
			return true;
		}

		if ( \post_type_supports( $post_type, 'pronamic_moneybird_project' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string $post_type Post type.
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		if ( ! $this->post_type_supports_moneybird( $post_type ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_moneybird',
			\__( 'Moneybird', 'pronamic-moneybird' ),
			[ $this, 'meta_box' ],
			$post_type,
			'normal',
			'default'
		);
	}

	/**
	 * Manage posts columns.
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/manage_posts_columns/
	 * @param string[] $columns   Columns.
	 * @param string   $post_type Post type.
	 * @return string[]
	 */
	public function manage_posts_columns( $columns, $post_type ) {
		if ( ! $this->post_type_supports_moneybird( $post_type ) ) {
			return $columns;
		}

		$columns['pronamic_moneybird'] = \__( 'Moneybird', 'pronamic-moneybird' );

		$new_columns = [];

		foreach ( $columns as $name => $label ) {
			if ( \in_array( $name, [ 'comments', 'date' ], true ) ) {
				$new_columns['pronamic_moneybird'] = $columns['pronamic_moneybird'];
			}

			$new_columns[ $name ] = $label;
		}

		$columns = $new_columns;

		return $columns;
	}

	/**
	 * Manage posts custom column.
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/manage_posts_custom_column/
	 * @param string $column_name Column name.
	 * @param int    $post_id     Post ID.
	 */
	public function manage_posts_custom_column( $column_name, $post_id ) {
		if ( 'pronamic_moneybird' !== $column_name ) {
			return;
		}

		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$contact_id = \get_post_meta( $post_id, '_pronamic_moneybird_contact_id', true );

		if ( '' !== $contact_id ) {
			printf(
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

		$product_id = \get_post_meta( $post_id, '_pronamic_moneybird_product_id', true );

		if ( '' !== $product_id ) {
			printf(
				'<a href="%s" title="%s"><span class="dashicons dashicons-products"></span></a>',
				\esc_url(
					Product::get_remote_link_by_id(
						$administration_id,
						$product_id
					)
				),
				\esc_attr(
					\sprintf(
						/* translators: %s: Product ID. */
						__( 'Product ID: %s', 'pronamic-moneybird' ),
						$product_id
					)
				)
			);
		}
	}

	/**
	 * Meta box authentication.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function meta_box( // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Used in include.
		$post
	) {
		\wp_nonce_field( 'pronamic_moneybird_save', 'pronamic_moneybird_nonce' );

		include __DIR__ . '/../admin/meta-box-post.php';
	}

	/**
	 * Save authorization settings.
	 * 
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_moneybird_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_save' ) ) {
			return;
		}

		$keys = [
			'_pronamic_moneybird_contact_id',
			'_pronamic_moneybird_ledger_account_id',
			'_pronamic_moneybird_product_id',
			'_pronamic_moneybird_project_id',
		];

		foreach ( $keys as $key ) {
			if ( ! \array_key_exists( $key, $_POST ) ) {
				continue;
			}

			$value = \sanitize_text_field( \wp_unslash( $_POST[ $key ] ) );

			if ( '' === $value ) {
				\delete_post_meta( $post_id, $key );
			}

			if ( '' !== $value ) {
				\update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
