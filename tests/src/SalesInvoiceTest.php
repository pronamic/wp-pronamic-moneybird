<?php
/**
 * Sales invoice test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use PHPUnit\Framework\TestCase;

/**
 * Sales invoice test class
 * 
 * @covers \Pronamic\Moneybird\SalesInvoice
 */
final class SalesInvoiceTest extends TestCase {
	/**
	 * Test description parse.
	 *
	 * @return void
	 */
	public function test_description_parse() {
		$data = \json_decode( \file_get_contents( __DIR__ . '/../json/create-sales-invoice-response.json' ) );

		foreach ( $data->details as $detail ) {
			$result = \preg_match_all(
				'/#subscription_(?P<subscription_id>[0-9]+)/',
				$detail->description,
				$matches
			);

			if ( false === $result ) {
				throw new \Exception( 'Something went wrong finding subscription IDs in the Moneybird sales invoice detail description.' );
			}

			$subscription_ids = \array_key_exists( 'subscription_id', $matches ) ? $matches['subscription_id'] : [];

			$this->assertIsArray( $subscription_ids );
			$this->assertContains( '1', $subscription_ids );
		}
	}
}
