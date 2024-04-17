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
