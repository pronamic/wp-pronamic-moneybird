<?php
/**
 * Remote serializer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use ReflectionObject;

/**
 * Remote serializer class
 */
final class RemoteSerializer {
	/**
	 * Context.
	 * 
	 * @var string
	 */
	public $context;

	/**
	 * Construct remote serializer.
	 * 
	 * @param string $context Context.
	 */
	public function __construct( $context = '' ) {
		$this->context = $context;
	}

	/**
	 * Serialize.
	 * 
	 * @param object $item Item.
	 * @return object
	 */
	public function serialize( $item ) {
		$data = [];

		$reflection_object = new ReflectionObject( $item );

		$properties = $reflection_object->getProperties();

		foreach ( $properties as $property ) {
			$value = $property->getValue( $item );

			if ( null === $value ) {
				continue;
			}

			$attributes = $property->getAttributes( RemoteApiProperty::class );

			foreach ( $attributes as $attribute ) {
				$remote_api_property = $attribute->newInstance();

				$data[ $remote_api_property->get_name( $this->context ) ] = $this->get_value( $value );
			}
		}

		return (object) $data;
	}

	/**
	 * Get value.
	 * 
	 * @param mixed $value Value.
	 * @return mixed
	 */
	private function get_value( $value ) {
		if ( $value instanceof RemoteSerializable ) {
			return $value->remote_serialize();
		}

		if ( \is_array( $value ) ) {
			return \array_map(
				[ $this, 'get_value' ],
				$value
			);
		}

		return $value;
	}
}
