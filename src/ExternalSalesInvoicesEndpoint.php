<?php
/**
 * External sales invoices endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;
use Pronamic\WordPress\Http\Facades\Http;

/**
 * External sales invoices endpoint class
 */
final class ExternalSalesInvoicesEndpoint extends ResourceEndpoint {
	/**
	 * Create external sales invoice.
	 *
	 * @link https://developer.moneybird.com/api/contacts/#post_contacts
	 * @param ExternalSalesInvoice $external_sales_invoice External sales invoice.
	 * @return ExternalSalesInvoice
	 */
	public function create_external_sales_invoice( ExternalSalesInvoice $external_sales_invoice ) {
		$url = $this->get_api_url( 'external_sales_invoices' );

		$data = [
			'external_sales_invoice' => $external_sales_invoice->remote_serialize( 'create' ),
		];

		$response = $this->client->post( $url, $data, '201' );

		$response_data = $response->json();

		return ExternalSalesInvoice::from_object( $response_data );
	}

	/**
	 * Add attachment to external sales invoices.
	 *
	 * @link https://developer.moneybird.com/api/external_sales_invoices/#post_external_sales_invoices_id_attachment
	 * @link https://github.com/pronamic/wp-lookup/blob/7f28c51974b5b8a418da1663565730165392d836/classes/BaseconeController.php#L571-L636
	 * @param ExternalSalesInvoice $external_sales_invoice External sales invoice.
	 * @param Attachment           $attachment             Attachment.
	 * @return void
	 * @throws \Exception Throws an exception if external sales invoice has no ID.
	 */
	public function add_attachment_to_external_sales_invoice( ExternalSalesInvoice $external_sales_invoice, Attachment $attachment ) {
		if ( null === $external_sales_invoice->id ) {
			throw new \Exception( 'Cannot add an attachment remotely to an external sales invoice without an ID.' );
		}

		$url = $this->get_api_url(
			\strtr(
				'external_sales_invoices/:id/attachment',
				[
					':id' => $external_sales_invoice->id,
				]
			)
		);

		$boundary = \hash( 'sha256', \uniqid( '', true ) );

		// Body.
		$body = '';

		$body .= '--' . $boundary . "\r\n";

		$body .= \sprintf(
			'Content-Disposition: attachment; name="file"; filename="%s"',
			$attachment->filename
		) . "\r\n";

		$body .= \sprintf(
			'Content-Type: %s',
			$attachment->type
		) . "\r\n";

		$body .= "\r\n";

		$body .= $attachment->contents . "\r\n";


		$body .= '--' . $boundary . '--';

		// Request.
		$response = Http::post(
			$url,
			[
				'headers' => [
					'Content-Type'  => 'multipart/mixed; boundary=' . $boundary,
					'Authorization' => 'Bearer ' . $this->client->api_token,
				],
				'body'    => $body,
				// phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout -- For file uploads this is acceptable.
				'timeout' => 30,
			]
		);

		$this->client->ensure_response_status( $response, '200' );
	}

	/**
	 * Add note to external sales invoice.
	 *
	 * @link https://developer.moneybird.com/api/external_sales_invoices/#post_external_sales_invoices_id_notes
	 * @param ExternalSalesInvoice $external_sales_invoice External sales invoice.
	 * @param Note                 $note                   Note.
	 * @return void
	 * @throws Exception Throws an exception if external sales invoice has no ID.
	 */
	public function add_note_to_external_sales_invoice( ExternalSalesInvoice $external_sales_invoice, Note $note ) {
		if ( null === $external_sales_invoice->id ) {
			throw new \Exception( 'Cannot add a note remotely to an external sales invoice without an ID.' );
		}

		$url = $this->get_api_url(
			\strtr(
				'external_sales_invoices/:id/notes',
				[
					':id' => $external_sales_invoice->id,
				]
			)
		);

		$data = [
			'note' => $note->remote_serialize( 'create' ),
		];

		$this->client->post( $url, $data, '201' );
	}
}
