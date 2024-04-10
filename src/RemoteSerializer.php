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
	 * Serialize.
	 * 
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

				$data[ $remote_api_property->name ] = $value instanceof RemoteSerializable ? $this->serialize( $value ) : $value;
			}
		}

		return (object) $data;
	}
}
