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

/**
 * Financial statement class
 */
final class FinancialStatement {
	/**
	 * ID.
	 * 
	 * @var int|null
	 */
	public $id;

	/**
	 * Financial account ID.
	 * 
	 * @var int
	 */
	public $financial_account_id;

	/**
	 * Reference.
	 * 
	 * @var string
	 */
	public $reference;

	/**
	 * Official date.
	 * 
	 * @param null|Date
	 */
	public $official_date;

	/**
	 * Official balance.
	 * 
	 * @param null|string
	 */
	public $official_balance;

	/**
	 * Importer key.
	 * 
	 * @param null|string
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
	public function __construct( $financial_account_id, $reference ) {
		$this->financial_account_id = $financial_account_id;
		$this->reference            = $reference;
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
				'official_date'                  => ( null === $this->official_date ) ? null : $this->official_date->format( 'Y-m-d' ),
				'official_balance'               => $this->official_balance,
				'importer_key'                   => $this->importer_key,
				'financial_mutations_attributes' => \array_map(
					function ( $financial_mutation ) {
						return $financial_mutation->get_create_parameters();
					},
					$this->financial_mutations
				),
			],
			function ( $value ) {
				return ( null !== $value );
			}
		);
	}

	/**
	 * From object.
	 * 
	 * @retrun self
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
				function ( $item ) {
					return FinancialMutation::from_object( $item );
				},
				$object_access->get_property( 'financial_mutations' )
			);
		}

		return $financial_statement;
	}
}
