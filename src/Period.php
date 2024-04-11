<?php
/**
 * Period
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

/**
 * Period class
 */
final class Period {
	/**
	 * Start date.
	 * 
	 * @var DateTimeInterface
	 */
	public $start_date;

	/**
	 * End date.
	 * 
	 * @var DateTimeInterface
	 */
	public $end_date;

	/**
	 * Construct period.
	 * 
	 * @param DateTimeInterface $start_date Start date.
	 * @param DateTimeInterface $end_date   End date.
	 */
	public function __construct( $start_date, $end_date ) {
		$this->start_date = $start_date;
		$this->end_date   = $end_date;
	}

	/**
	 * To string.
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->start_date->format( 'Ymd' ) . '..' . $this->end_date->format( 'Ymd' );
	}

	/**
	 * From string.
	 * 
	 * @param string $value Value.
	 * @return self
	 * @throws \InvalidArgumentException Throws an exception if the string cannot be converted to a period object.
	 */
	public static function from_string( $value ) {
		$start_date = DateTimeImmutable::createFromFormat( 'Ymd', \substr( $value, 0, 8 ), new DateTimeZone( 'UTC' ) );
		$end_date   = DateTimeImmutable::createFromFormat( 'Ymd', \substr( $value, 10, 8 ), new DateTimeZone( 'UTC' ) );

		if ( false === $start_date ) {
			throw new \InvalidArgumentException( \esc_html( 'Cannot read start date from period string: ' . $value ) );
		}

		if ( false === $end_date ) {
			throw new \InvalidArgumentException( \esc_html( 'Cannot read end date from period string: ' . $value ) );
		}

		return new self(
			$start_date->setTime( 0, 0, 0 ),
			$end_date->setTime( 23, 59, 59 )
		);
	}
}
