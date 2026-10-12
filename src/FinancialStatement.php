<?php
/**
 * Financial statement
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use JsonSerializable;

/**
 * Financial statement class
 */
final class FinancialStatement implements JsonSerializable {
	/**
	 * ID.
	 * 
	 * @var int|null
	 */
	public $id;

	/**
	 * Official date.
	 * 
	 * @var null|Date
	 */
	public $official_date;

	/**
	 * Official balance.
	 * 
	 * @var null|string
	 */
	public $official_balance;

	/**
	 * Importer key.
	 * 
	 * @var null|string
	 */
	public $importer_key;

	/**
	 * Financial mutations.
	 * 
	 * @var array
	 */
	public $financial_mutations = [];

	/**
	 * Construct financial statement.
	 * 
	 * @param int    $financial_account_id Financial account ID.
	 * @param string $reference            Reference.
	 */
	public function __construct(
		/**
		 * Financial account ID.
		 */
		public $financial_account_id,
		/**
		 * Reference.
		 */
		public $reference
	) {
	}

	/**
	 * Get create parameters.
	 * 
	 * @link https://developer.moneybird.com/api/financial_statements/#post_financial_statements
	 * @return array
	 */
	public function get_create_parameters() {
		return \array_filter(
			[
				'financial_account_id'           => $this->financial_account_id,
				'reference'                      => $this->reference,
				'official_date'                  => $this->official_date?->format( 'Y-m-d' ),
				'official_balance'               => $this->official_balance,
				'importer_key'                   => $this->importer_key,
				'financial_mutations_attributes' => \array_map(
					fn( $financial_mutation ) => $financial_mutation->get_create_parameters(),
					$this->financial_mutations
				),
			],
			fn( $value ) => null !== $value
		);
	}

	/**
	 * JSON serialize.
	 * 
	 * @return object
	 */
	public function jsonSerialize() {
		return (object) \array_filter(
			[
				'financial_account_id' => $this->financial_account_id,
				'reference'            => $this->reference,
				'official_date'        => $this->official_date?->format( 'Y-m-d' ),
				'official_balance'     => $this->official_balance,
				'importer_key'         => $this->importer_key,
				'financial_mutations'  => \array_map(
					fn( $financial_mutation ) => $financial_mutation->jsonSerialize(),
					$this->financial_mutations
				),
			],
			fn( $value ) => null !== $value
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

		$financial_statement = new self(
			$object_access->get_property( 'financial_account_id' ),
			$object_access->get_property( 'reference' )
		);

		$financial_statement->id               = $object_access->get_optional( 'id' );
		$financial_statement->official_date    = $object_access->get_optional_date( 'official_date' );
		$financial_statement->official_balance = $object_access->get_optional( 'id' );
		$financial_statement->importer_key     = $object_access->get_optional( 'importer_key' );

		if ( $object_access->has_property( 'financial_mutations' ) ) {
			$financial_statement->financial_mutations = \array_map(
				fn( $item ) => FinancialMutation::from_object( $item ),
				$object_access->get_property( 'financial_mutations' )
			);
		}

		return $financial_statement;
	}
}
