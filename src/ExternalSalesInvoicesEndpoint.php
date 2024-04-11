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
			'external_sales_invoice' => $external_sales_invoice->get_create_parameters(),
		];

		$response = $this->client->post( $url, $data );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}

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
				'timeout' => 30,
			]
		);

		$response_status = (string) $response->status();

		if ( '200' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			$response_data = $response->json();

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}
	}
}
