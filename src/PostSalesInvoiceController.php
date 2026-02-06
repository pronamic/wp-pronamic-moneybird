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
		\add_action( 'add_meta_boxes', $this->add_meta_boxes( ... ) );

		\add_action( 'save_post', $this->save_post( ... ) );
	}

	/**
	 * Add meta boxes.
	 *
	 * @param string $post_type Post type.
	 * @return void
	 */
	private function add_meta_boxes( $post_type ) {
		if ( ! \post_type_supports( $post_type, 'pronamic_moneybird_sales_invoice' ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_moneybird_sales_invoice',
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
		\wp_nonce_field( 'pronamic_moneybird_sales_invoice_post_save', 'pronamic_moneybird_sales_invoice_nonce' );

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

		if ( ! \array_key_exists( 'pronamic_moneybird_sales_invoice_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_sales_invoice_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_sales_invoice_post_save' ) ) {
			return;
		}

		if ( isset( $_POST['_pronamic_moneybird_sales_invoice'] ) && \is_array( $_POST['_pronamic_moneybird_sales_invoice'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each field sanitized individually below.
			$input = \wp_unslash( $_POST['_pronamic_moneybird_sales_invoice'] );
			$data  = [];

			if ( isset( $input['details_attributes'] ) && \is_array( $input['details_attributes'] ) ) {
				$details = [];

				foreach ( $input['details_attributes'] as $detail_input ) {
					if ( ! \is_array( $detail_input ) ) {
						continue;
					}

					$detail = [
						'amount'      => isset( $detail_input['amount'] ) ? \sanitize_text_field( $detail_input['amount'] ) : '',
						'description' => isset( $detail_input['description'] ) ? \sanitize_textarea_field( $detail_input['description'] ) : '',
						'price'       => isset( $detail_input['price'] ) ? \sanitize_text_field( $detail_input['price'] ) : '',
						'product_id'  => isset( $detail_input['product_id'] ) ? \sanitize_text_field( $detail_input['product_id'] ) : '',
						'project_id'  => isset( $detail_input['project_id'] ) ? \sanitize_text_field( $detail_input['project_id'] ) : '',
					];

					$detail = \array_filter( $detail );

					if ( ! empty( $detail ) ) {
						$details[] = $detail;
					}
				}

				if ( ! empty( $details ) ) {
					$data['details_attributes'] = $details;
				}
			}

			\update_post_meta( $post_id, '_pronamic_moneybird_sales_invoice', \wp_slash( \wp_json_encode( $data ) ) );
		}
	}
}
