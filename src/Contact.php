<?php
/**
 * Contact
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;
use ReflectionObject;

/**
 * Contact class
 */
final class Contact implements RemoteSerializable {
	/**
	 * ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'id' )]
	public $id;

	/**
	 * Administration ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'administration_id' )]
	public $administration_id;

	/**
	 * Company name.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'company_name' )]
	public $company_name;

	/**
	 * Address 1.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'address1' )]
	public $address_1;

	/**
	 * Address 2.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'address2' )]
	public $address_2;

	/**
	 * ZIP Code.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'zipcode' )]
	public $zip_code;

	/**
	 * City.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'city' )]
	public $city;

	/**
	 * Country.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'country' )]
	public $country_code;

	/**
	 * Phone.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'phone' )]
	public $phone;

	/**
	 * Delivery method.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'delivery_method' )]
	public $delivery_method;

	/**
	 * Customer ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'customer_id' )]
	public $customer_id;

	/**
	 * Tax number.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'tax_number' )]
	public $tax_number;

	/**
	 * First name.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'firstname' )]
	public $first_name;

	/**
	 * Last name.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'lastname' )]
	public $last_name;

	/**
	 * Chamber of commerce.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'chamber_of_commerce' )]
	public $chamber_of_commerce;

	/**
	 * Bank account.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'bank_account' )]
	public $bank_account;

	/**
	 * Send invoices to attention.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'send_invoices_to_attention' )]
	public $send_invoices_to_attention;

	/**
	 * Send invoices to email.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'send_invoices_to_email' )]
	public $send_invoices_to_email;

	/**
	 * Send estimates to attention.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'send_estimates_to_attention' )]
	public $send_estimates_to_attention;

	/**
	 * Send estimates to email.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'send_estimates_to_email' )]
	public $send_estimates_to_email;

	/**
	 * SEPA active.
	 * 
	 * @var bool|null
	 */
	#[RemoteApiProperty( 'sepa_active' )]
	public $sepa_active;

	/**
	 * SEPA IBAN.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_iban' )]
	public $sepa_iban;

	/**
	 * SEPA IBAN account name.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_iban_account_name' )]
	public $sepa_iban_account_name;

	/**
	 * SEPA BIC.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_bic' )]
	public $sepa_bic;

	/**
	 * SEPA mandate ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_mandate_id' )]
	public $sepa_mandate_id;

	/**
	 * SEPA mandate date.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_mandate_date' )]
	public $sepa_mandate_date;

	/**
	 * SEPA sequence type
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'sepa_sequence_type' )]
	public $sepa_sequence_type;

	/**
	 * SI indentifier type.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'si_identifier_type' )]
	public $si_identifier_type;

	/**
	 * SI indentifier.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'si_identifier' )]
	public $si_identifier;

	/**
	 * Invoice workflow ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'invoice_workflow_id' )]
	public $invoice_workflow_id;

	/**
	 * Estimate workflow ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'estimate_workflow_id' )]
	public $estimate_workflow_id;

	/**
	 * Email UBL.
	 * 
	 * @var bool|null
	 */
	#[RemoteApiProperty( 'email_ubl' )]
	public $email_ubl;

	/**
	 * Direct debit.
	 * 
	 * @var bool|null
	 */
	#[RemoteApiProperty( 'direct_debit' )]
	public $direct_debit;

	/**
	 * Custom fields.
	 * 
	 * @var array|null
	 */
	public $custom_fields;

	/**
	 * Contact person.
	 * 
	 * Please note: a contact without a company name is a private individual
	 * and cannot contain a contact person. This is not well documented by 
	 * Moneybird, it will result in the following error message:
	 * 
	 * ```json
	 * {
	 *     "error": {
	 *         "company_name": [
	 *             "is verplicht"
	 *         ],
	 *         "firstname": [
	 *             "is verplicht"
	 *         ],
	 *         "lastname": [
	 *             "is verplicht"
	 *         ]
	 *     }
	 * }
	 * ```
	 *
	 * @var ContactPerson|null
	 */
	#[RemoteApiProperty( 'contact_person' )]
	public $contact_person;

	/**
	 * Get remote link.
	 * 
	 * @return string
	 * @throws \Exception Throws an exception if remote link cannot be constructed.
	 */
	public function get_remote_link() {
		if ( null === $this->administration_id ) {
			throw new \Exception( 'Contact administration ID is undefined, remote link cannot be constructed.' );
		}

		if ( null === $this->id ) {
			throw new \Exception( 'Contact ID is undefined, remote link cannot be constructed.' );
		}

		return self::get_remote_link_by_id( $this->administration_id, $this->id );
	}

	/**
	 * Remote serialize.
	 * 
	 * @link https://developer.moneybird.com/api/financial_statements/#post_financial_statements
	 * @link https://www.php.net/manual/en/language.attributes.overview.php#127899
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		$serializer = new RemoteSerializer( $context );

		return $serializer->serialize( $this );
	}

	/**
	 * Get similarity report.
	 * 
	 * @return SimilarityReport
	 */
	public function get_similarity_report( Contact $contact ) {
		$report = new SimilarityReport();

		$properties = [
			'company_name',
			'address_1',
			'address_2',
			'zip_code',
			'city',
			'country_code',
			'send_invoices_to_email',
		];

		$contact_1 = $this;
		$contact_2 = $contact;

		foreach ( $properties as $property ) {
			$value_1 = (string) $contact_1->{$property};
			$value_2 = (string) $contact_2->{$property};

			\similar_text( $value_1, $value_2, $percent );

			if ( '' === $value_1 && '' === $value_2 ) {
				$percent = 100;
			}

			$report->property_similarities[ $property ] = $percent;
		}

		return $report;
	}

	/**
	 * From object.
	 * 
	 * @param object $data Data.
	 * @return self
	 */
	public static function from_object( $data ) {
		$contact = new self();

		$unserializer = new RemoteUnserializer();

		$unserializer->unserialize( $contact, $data );

		return $contact;
	}

	/**
	 * Get remote link by ID.
	 * 
	 * @param string $administration_id Administration ID.
	 * @param string $contact_id        Contact ID.
	 * @return string
	 */
	public static function get_remote_link_by_id( $administration_id, $contact_id ) {
		return \strtr(
			'https://moneybird.com/:administration_id/contacts/:id',
			[
				':administration_id' => $administration_id,
				':id'                => $contact_id,
			]
		);
	}
}
