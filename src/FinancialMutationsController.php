<?php
/**
 * Financial mutations controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use WP_CLI;

/**
 * Financial mutations controller class
 */
final class FinancialMutationsController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'cli_init', $this->cli_init( ... ) );
	}

	/**
	 * WP-CLI initialize.
	 *
	 * @link https://github.com/wp-cli/wp-cli/blob/9aec20fd711a8b7442cc2f89e32af276e3f16045/php/WP_CLI/Runner.php#L1724
	 * @return void
	 */
	public function cli_init() {
		WP_CLI::add_command( 'pronamic-moneybird link-financial-mutations-to-external-sales-invoices-by-transaction-id-notes', $this->cli_link_financial_mutations_to_external_sales_invoices_by_transaction_id_notes( ... ) );
	}

	/**
	 * Link unprocessed financial mutations to open external sales invoices that mention the transaction ID of the financial mutation in one of their notes.
	 *
	 * ## OPTIONS
	 *
	 * --period=<period>
	 * : Moneybird period filter, for example `202601..202604`.
	 *
	 * --financial-account-id=<financial-account-id>
	 * : Moneybird financial account ID of the financial mutations.
	 *
	 * [--prefix=<prefix>]
	 * : Only process financial mutations with a transaction ID that starts with this prefix.
	 * ---
	 * default: tr_
	 * ---
	 *
	 * [--dry-run]
	 * : Only show the matches, do not link anything.
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * ## EXAMPLES
	 *
	 *     wp pronamic-moneybird link-financial-mutations-to-external-sales-invoices-by-transaction-id-notes --period=202601..202604 --financial-account-id=123456789 --dry-run
	 *
	 * @link https://github.com/pronamic/orbis.pronamic.nl/issues/75
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function cli_link_financial_mutations_to_external_sales_invoices_by_transaction_id_notes( $args, $assoc_args ) {
		$assoc_args = \wp_parse_args(
			$assoc_args,
			[
				'period'               => '',
				'financial-account-id' => '',
				'prefix'               => 'tr_',
			]
		);

		$period               = (string) $assoc_args['period'];
		$financial_account_id = (string) $assoc_args['financial-account-id'];
		$prefix               = (string) $assoc_args['prefix'];

		if ( '' === $period ) {
			WP_CLI::error( 'The `--period` argument is required, for example `--period=202601..202604`.' );
		}

		if ( '' === $financial_account_id ) {
			WP_CLI::error( 'The `--financial-account-id` argument is required.' );
		}

		$dry_run = (bool) \WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', false );

		/**
		 * Moneybird client.
		 */
		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$api_token = \get_post_meta( $authorization_id, '_pronamic_moneybird_api_token', true );

		$client = new Client( $api_token );

		$administration_endpoint = $client->get_administration_endpoint( $administration_id );

		$external_sales_invoices_endpoint = $administration_endpoint->get_external_sales_invoices_endpoint();

		$financial_mutations_endpoint = $administration_endpoint->get_financial_mutations_endpoint();

		/**
		 * Financial mutations.
		 */
		$financial_mutations = $this->get_all_pages(
			$financial_mutations_endpoint->get_financial_mutations( ... ),
			[
				'filter' => \implode(
					',',
					[
						'period:' . $period,
						'state:unprocessed',
						'financial_account_id:' . $financial_account_id,
					]
				),
			]
		);

		WP_CLI::log( 'Unprocessed financial mutations: ' . \count( $financial_mutations ) );

		/**
		 * External sales invoices.
		 */
		$external_sales_invoices = $this->get_all_pages(
			$external_sales_invoices_endpoint->get_external_sales_invoices( ... ),
			[
				'filter' => \implode(
					',',
					[
						'period:' . $period,
						'state:open',
					]
				),
			]
		);

		WP_CLI::log( 'Open external sales invoices: ' . \count( $external_sales_invoices ) );

		/**
		 * Match.
		 */
		$items = [];

		$to_link = [];

		$linked_external_sales_invoice_ids = [];

		foreach ( $financial_mutations as $financial_mutation ) {
			$transaction_id = (string) $financial_mutation->account_servicer_transaction_id;

			if ( '' === $transaction_id ) {
				continue;
			}

			if ( ! \str_starts_with( $transaction_id, $prefix ) ) {
				continue;
			}

			$item = [
				'transaction_id'            => $transaction_id,
				'financial_mutation_id'     => $financial_mutation->id,
				'amount'                    => $financial_mutation->amount,
				'external_sales_invoice_id' => '',
				'total_unpaid'              => '',
				'status'                    => '',
			];

			if ( (float) $financial_mutation->amount <= 0 ) {
				$item['status'] = 'skipped, amount is not positive';

				$items[] = $item;

				continue;
			}

			$matches = $this->find_external_sales_invoices_by_note( $external_sales_invoices, $transaction_id );

			if ( 0 === \count( $matches ) ) {
				$item['status'] = 'no match';

				$items[] = $item;

				continue;
			}

			if ( \count( $matches ) > 1 ) {
				$item['external_sales_invoice_id'] = \implode( ', ', \array_column( $matches, 'id' ) );
				$item['status']                    = 'skipped, multiple matches';

				$items[] = $item;

				continue;
			}

			$external_sales_invoice = $matches[0];

			$item['external_sales_invoice_id'] = $external_sales_invoice->id;
			$item['total_unpaid']              = $external_sales_invoice->total_unpaid;

			if ( \in_array( $external_sales_invoice->id, $linked_external_sales_invoice_ids, true ) ) {
				$item['status'] = 'skipped, invoice already matched';

				$items[] = $item;

				continue;
			}

			if ( ! $this->amounts_are_equal( $financial_mutation->amount, $external_sales_invoice->total_unpaid ) ) {
				$item['status'] = 'skipped, amount mismatch';

				$items[] = $item;

				continue;
			}

			$item['status'] = 'match';

			$items[] = $item;

			$to_link[] = [ $financial_mutation, $external_sales_invoice ];

			$linked_external_sales_invoice_ids[] = $external_sales_invoice->id;
		}

		\WP_CLI\Utils\format_items(
			'table',
			$items,
			[
				'transaction_id',
				'financial_mutation_id',
				'amount',
				'external_sales_invoice_id',
				'total_unpaid',
				'status',
			]
		);

		WP_CLI::log( 'Matches: ' . \count( $to_link ) );

		if ( 0 === \count( $to_link ) ) {
			return;
		}

		if ( $dry_run ) {
			WP_CLI::success( 'Dry run, no financial mutations have been linked.' );

			return;
		}

		/**
		 * Link.
		 */
		WP_CLI::confirm(
			\sprintf(
				'Do you want to link %d financial mutations to external sales invoices?',
				\count( $to_link )
			),
			$assoc_args
		);

		$number_linked = 0;
		$number_failed = 0;

		foreach ( $to_link as [ $financial_mutation, $external_sales_invoice ] ) {
			try {
				$financial_mutations_endpoint->link_booking(
					$financial_mutation,
					'ExternalSalesInvoice',
					$external_sales_invoice->id,
					$financial_mutation->amount
				);

				++$number_linked;

				WP_CLI::log(
					\sprintf(
						'Linked financial mutation %s (%s) to external sales invoice %s.',
						$financial_mutation->id,
						$financial_mutation->account_servicer_transaction_id,
						$external_sales_invoice->id
					)
				);
			} catch ( \Exception $e ) {
				++$number_failed;

				WP_CLI::error(
					\sprintf(
						'Could not link financial mutation %s (%s) to external sales invoice %s: %s',
						$financial_mutation->id,
						$financial_mutation->account_servicer_transaction_id,
						$external_sales_invoice->id,
						$e->getMessage()
					),
					false
				);
			}
		}

		if ( $number_failed > 0 ) {
			WP_CLI::error(
				\sprintf(
					'Linked %d financial mutations, %d failed.',
					$number_linked,
					$number_failed
				)
			);
		}

		WP_CLI::success(
			\sprintf(
				'Linked %d financial mutations.',
				$number_linked
			)
		);
	}

	/**
	 * Get the items of all pages of a paginated Moneybird list.
	 *
	 * @link https://developer.moneybird.com/pagination/
	 * @param callable $get_page   Callback that returns the items for the given parameters.
	 * @param array    $parameters Parameters.
	 * @return array
	 */
	private function get_all_pages( callable $get_page, array $parameters ) {
		$items = [];

		$per_page = 100;

		$page = 1;

		do {
			$page_items = $get_page(
				\array_merge(
					$parameters,
					[
						'per_page' => $per_page,
						'page'     => $page,
					]
				)
			);

			$items = \array_merge( $items, $page_items );

			$number_page_items = \count( $page_items );

			++$page;
		} while ( $number_page_items === $per_page );

		return $items;
	}

	/**
	 * Find the external sales invoices that mention the needle in one of their notes.
	 *
	 * @param ExternalSalesInvoice[] $external_sales_invoices External sales invoices.
	 * @param string                 $needle                  Needle.
	 * @return ExternalSalesInvoice[]
	 */
	private function find_external_sales_invoices_by_note( array $external_sales_invoices, $needle ) {
		$matches = [];

		foreach ( $external_sales_invoices as $external_sales_invoice ) {
			$notes = \is_array( $external_sales_invoice->notes ) ? $external_sales_invoice->notes : [];

			foreach ( $notes as $note ) {
				$text = $note->note ?? null;

				if ( ! \is_string( $text ) ) {
					continue;
				}

				if ( \str_contains( $text, $needle ) ) {
					$matches[] = $external_sales_invoice;

					continue 2;
				}
			}
		}

		return $matches;
	}

	/**
	 * Check if two Moneybird amounts are equal, regardless of their notation.
	 *
	 * @param string|null $amount_a Amount A.
	 * @param string|null $amount_b Amount B.
	 * @return bool
	 */
	private function amounts_are_equal( $amount_a, $amount_b ) {
		if ( ! \is_numeric( $amount_a ) || ! \is_numeric( $amount_b ) ) {
			return false;
		}

		return (int) \round( (float) $amount_a * 100 ) === (int) \round( (float) $amount_b * 100 );
	}
}
