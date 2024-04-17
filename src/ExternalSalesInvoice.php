<?php
/**
 * External sales invoice
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * External sales invoice class
 */
final class ExternalSalesInvoice implements RemoteSerializable {
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
	 * Contact ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'contact_id' )]
	public $contact_id;

	/**
	 * Reference.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'reference' )]
	public $reference;

	/**
	 * Date.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'date' )]
	public $date;

	/**
	 * Due date
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'due_date' )]
	public $due_date;

	/**
	 * Currency.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'currency' )]
	public $currency;

	/**
	 * Prices include tax
	 * 
	 * @var bool|null
	 */
	#[RemoteApiProperty( 'prices_are_incl_tax' )]
	public $prices_are_incl_tax;

	/**
	 * Source.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'source' )]
	public $source;

	/**
	 * Source URL.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'source_url' )]
	public $source_url;

	/**
	 * Details.
	 * 
	 * @var ExternalSalesInvoiceDetail[]|null
	 */
	#[RemoteApiProperty( 'details', 'details_attributes' )]
	public $details;

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
	 * @link https://developer.moneybird.com/api/external_sales_invoices/#post_external_sales_invoices
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		$serializer = new RemoteSerializer( $context );

		return $serializer->serialize( $this );
	}

	/**
	 * From object.
	 * 
	 * @param object $data Data.
	 * @return self
	 */
	public static function from_object( $data ) {
		$external_sales_invoice = new self();

		$unserializer = new RemoteUnserializer();

		$unserializer->unserialize( $external_sales_invoice, $data );

		return $external_sales_invoice;
	}

	/**
	 * Get remote link by ID.
	 * 
	 * @param string $administration_id         Administration ID.
	 * @param string $external_sales_invoice_id External sales invoice ID.
	 * @return string
	 */
	public static function get_remote_link_by_id( $administration_id, $external_sales_invoice_id ) {
		return \strtr(
			'https://moneybird.com/:administration_id/external_sales_invoices/:id',
			[
				':administration_id' => $administration_id,
				':id'                => $external_sales_invoice_id,
			]
		);
	}
}
