<?php
/**
 * Post sales invoice controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Post sales invoice controller class
 */
final class PostSalesInvoiceController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', $this->init( ... ) );

		\add_action( 'add_meta_boxes', $this->add_meta_boxes( ... ) );

		\add_action( 'save_post', $this->save_post( ... ) );
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	private function init() {
		\add_post_type_support( 'post', 'pronamic_moneybird_sales_invoice_details' );
	}

	/**
	 * Add meta boxes.
	 *
	 * @param string $post_type Post type.
	 * @return void
	 */
	private function add_meta_boxes( $post_type ) {
		if ( ! \post_type_supports( $post_type, 'pronamic_moneybird_sales_invoice_details' ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_moneybird',
			\__( 'Moneybird sales invoice', 'pronamic-moneybird' ),
			$this->meta_box( ... ),
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
	private function meta_box( // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Used in include.
		$post
	) {
		\wp_nonce_field( 'pronamic_moneybird_sales_invoice_post_save', 'pronamic_moneybird_nonce' );

		include __DIR__ . '/../admin/meta-box-post-sales-invoice.php';
	}

	/**
	 * Save authorization settings.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	private function save_post( $post_id ) {
		if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_moneybird_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_sales_invoice_post_save' ) ) {
			return;
		}

		if ( isset( $_POST['_pronamic_moneybird_sales_invoice'] ) ) {
			$data = \map_deep( $_POST['_pronamic_moneybird_sales_invoice'], 'sanitize_text_field' );

			if ( \array_key_exists( 'details_attributes', $data ) && \is_array( $data['details_attributes'] ) ) {
				$data['details_attributes'] = \array_filter(
					\array_map(
						function ( $data ) {
							return \is_array( $data ) ? \array_filter( $data ) : $data;
						},
						$data['details_attributes']
					)
				);
			}

			\update_post_meta( $post_id, '_pronamic_moneybird_sales_invoice', \wp_slash( \wp_json_encode( $data ) ) );
		}
	}
}
