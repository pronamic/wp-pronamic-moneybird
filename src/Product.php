<?php
/**
 * Product
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;

/**
 * Product class
 */
final class Product {
	/**
	 * Get remote link by ID.
	 * 
	 * @param string $administration_id Administration ID.
	 * @param string $product_id        Product ID.
	 * @return string
	 */
	public static function get_remote_link_by_id( $administration_id, $product_id ) {
		return \strtr(
			'https://moneybird.com/:administration_id/products/:id',
			[
				':administration_id' => $administration_id,
				':id'                => $product_id,
			]
		);
	}
}
