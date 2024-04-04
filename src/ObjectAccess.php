<?php
/**
 * Object access
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Object access class
 */
final class ObjectAccess {
	/**
	 * Object.
	 *
	 * @var object Object.
	 */
	private $value;

	/**
	 * Construct object access.
	 *
	 * @param object $value Object.
	 */
	public function __construct( object $value ) {
		$this->value = $value;
	}

	/**
	 * Checks if the object has a property.
	 *
	 * @param string $property Property.
	 * @return bool True if the property exists, false if it doesn't exist.
	 */
	public function has_property( string $property ) {
		return \property_exists( $this->value, $property );
	}

	/**
	 * Get property.
	 *
	 * @param string $property Property.
	 * @return mixed
	 * @throws \Exception Throws exception when property does not exists.
	 */
	public function get_property( string $property ) {
		if ( ! \property_exists( $this->value, $property ) ) {
			throw new \Exception(
				\sprintf(
					'Object does not have `%s` property.',
					\esc_html( $property )
				)
			);
		}

		return $this->value->{$property};
	}

	/**
	 * Get optional.
	 *
	 * @param string $property Property.
	 * @return mixed
	 */
	public function get_optional( $property ) {
		if ( ! \property_exists( $this->value, $property ) ) {
			return null;
		}

		return $this->value->{$property};
	}

	/**
	 * Get date.
	 * 
	 * @param string $property Property.
	 * @return Date
	 */
	public function get_date( $property ) {
		return Date::from_string( $this->get_property( ( $property ) ) );
	}

	/**
	 * Get optional date.
	 *
	 * @param string $property Property.
	 * @return mixed
	 */
	public function get_optional_date( $property ) {
		$value = $this->get_optional( $property );

		if ( null === $value ) {
			return null;
		}

		return Date::from_string( $value );
	}
}
