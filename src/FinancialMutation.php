<?php
/**
 * Financial mutation
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Financial mutation class
 */
final class FinancialMutation {
	/**
	 * ID.
	 * 
	 * @var int|null
	 */
	public $id;

	/**
	 * Date.
	 * 
	 * @var Date|null
	 */
	public $date;

	/**
	 * Message.
	 * 
	 * @var string|null
	 */
	public $message;

	/**
	 * Amount.
	 * 
	 * @var string|null
	 */
	public $amount;

	/**
	 * Code.
	 * 
	 * @var string|null
	 */
	public $code;

	/**
	 * Contra account name.
	 * 
	 * @var string|null
	 */
	public $contra_account_name;

	/**
	 * Contra account number.
	 * 
	 * @var string|null
	 */
	public $contra_account_number;

	/**
	 * Batch reference.
	 * 
	 * @var string|null
	 */
	public $batch_reference;

	/**
	 * Offset
	 * 
	 * @var int|null
	 */
	public $offset;

	/**
	 * Account_servicer transaction id
	 * 
	 * @var string|null
	 */
	public $account_servicer_transaction_id;

	/**
	 * Account_servicer metadata
	 * 
	 * @var mixed|null
	 */
	public $account_servicer_metadata;

	/**
	 * Get create parameters.
	 * 
	 * @link https://developer.moneybird.com/api/financial_statements/#post_financial_statements
	 * @return array
	 */
	public function get_create_parameters() {
		return \array_filter(
			[
				'date'                            => ( null === $this->date ) ? null : $this->date->format( 'Y-m-d' ),
				'message'                         => $this->message,
				'amount'                          => $this->amount,
				'code'                            => $this->code,
				'contra_account_name'             => $this->contra_account_name,
				'contra_account_number'           => $this->contra_account_number,
				'batch_reference'                 => $this->batch_reference,
				'offset'                          => $this->offset,
				'account_servicer_transaction_id' => $this->account_servicer_transaction_id,
				'account_servicer_metadata'       => $this->account_servicer_metadata,
			],
			function ( $value ) {
				return ( null !== $value );
			}
		);
	}

	/**
	 * From object.
	 * 
	 * @param object $data Data.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$financial_mutation = new self();

		$financial_mutation->id                              = $object_access->get_optional( 'id' );
		$financial_mutation->date                            = $object_access->get_date( 'date' );
		$financial_mutation->message                         = $object_access->get_optional( 'message' );
		$financial_mutation->amount                          = $object_access->get_optional( 'amount' );
		$financial_mutation->code                            = $object_access->get_optional( 'code' );
		$financial_mutation->contra_account_name             = $object_access->get_optional( 'contra_account_name' );
		$financial_mutation->contra_account_name             = $object_access->get_optional( 'contra_account_number' );
		$financial_mutation->batch_reference                 = $object_access->get_optional( 'batch_reference' );
		$financial_mutation->offset                          = $object_access->get_optional( 'offset' );
		$financial_mutation->account_servicer_transaction_id = $object_access->get_optional( 'account_servicer_transaction_id' );
		$financial_mutation->account_servicer_metadata       = $object_access->get_optional( 'account_servicer_metadata' );

		return $financial_mutation;
	}
}
