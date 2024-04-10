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
	 * @var array|null
	 */
	#[RemoteApiProperty( 'details', 'details_attributes' )]
	public $details;

	/**
	 * Get create parameters.
	 * 
	 * @link https://developer.moneybird.com/api/external_sales_invoices/#post_external_sales_invoices
	 * @return array
	 */
	public function get_create_parameters() {
		$serializer = new RemoteSerializer();

		$serializer->context = 'create';

		return $serializer->serialize( $this );
	}

	/**
	 * From object.
	 * 
	 * @retrun self
	 */
	public static function from_object( $data ) {
		$external_sales_invoice = new self();

		$unserializer = new RemoteUnserializer();

		$unserializer->unserialize( $external_sales_invoice, $data );

		return $external_sales_invoice;
	}
}
