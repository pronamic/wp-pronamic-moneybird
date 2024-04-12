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
			'contact' => $contact->remote_serialize( 'create' ),
		];

		$response = $this->client->post( $url, $data, '201' );

		$response_data = $response->json();

		return Contact::from_object( $response_data );
	}

	/**
	 * Get contacts.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#get_contacts
	 * @param array $parameters Parameters.
	 * @return array
	 * @throws Error Throws an exception if get contacts fails.
	 */
	public function get_contacts( array $parameters = [] ) {
		$url = $this->get_api_url( 'contacts' );

		$response = $this->client->get( $url, $parameters, '200' );

		$response_data = $response->json();

		return $response_data;
	}
}
