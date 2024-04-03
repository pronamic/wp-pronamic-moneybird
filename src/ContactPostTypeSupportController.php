<?php
/**
 * Contact post type support controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Contact post type support controller class
 */
final class ContactPostTypeSupportController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );

		\add_action( 'save_post', [ $this, 'save_post' ] );
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string $post_type Post type.
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		if ( ! \post_type_supports( $post_type, 'pronamic_moneybird_contact' ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_moneybird_contact',
			\__( 'Moneybird contact', 'pronamic-moneybird' ),
			[ $this, 'meta_box_contact' ],
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
	public function meta_box_contact( // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Used in include.
		$post
	) {
		\wp_nonce_field( 'pronamic_moneybird_save_contact', 'pronamic_moneybird_nonce' );

		include __DIR__ . '/../admin/meta-box-contact.php';
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

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_save_contact' ) ) {
			return;
		}

		$contact_id = \array_key_exists( '_pronamic_moneybird_contact_id', $_POST ) ? \sanitize_text_field( \wp_unslash( $_POST['_pronamic_moneybird_contact_id'] ) ) : '';

		\update_post_meta( $post_id, '_pronamic_moneybird_contact_id', $contact_id );
	}
}
