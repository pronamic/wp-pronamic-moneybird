<?php
/**
 * Date
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Date class
 */
final class Date extends DateTimeImmutable {
	/**
	 * Date from string.
	 * 
	 * @return Date
	 */
	public static function from_string( $value ) {
		$result = self::createFromFormat( 'Y-m-d', $value, new DateTimeZone( 'UTC' ) );

		if ( false === $result ) {
			throw new Exception( 'Unexteded date format: ' . wp_json_encode( $value ) );
		}

		$result = $result->setTime( 0, 0 );

		return $result;
	}
}
