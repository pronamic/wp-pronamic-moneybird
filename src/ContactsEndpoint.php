<?php
/**
 * Contacts endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;

/**
 * Contacts endpoint class
 */
final class ContactsEndpoint extends ResourceEndpoint {
	/**
	 * Create contact.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#post_contacts
	 * @param Contact $contact Contact.
	 * @return Contact
	 */
	public function create_contact( Contact $contact ) {
		$url = $this->get_api_url( 'contacts' );

		$data = [
			'contact' => $contact->get_create_parameters(),
		];

		$response = $this->client->post( $url, $data );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}

		return Contact::from_object( $response_data );
	}

	/**
	 * Get contacts.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#get_contacts
	 * @param array $parameters Parameters.
	 * @return array
	 */
	public function get_contacts( array $parameters = [] ) {
		$url = $this->get_api_url( 'contacts' );

		$response = $this->client->get( $url, $parameters );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '200' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}

		return $response_data;
	}
}
