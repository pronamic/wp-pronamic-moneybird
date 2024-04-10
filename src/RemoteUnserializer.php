<?php
/**
 * Remote unserializer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use ReflectionObject;

/**
 * Remote unserializer class
 */
final class RemoteUnserializer {
	/**
	 * Serialize.
	 * 
	 * @param object $data Data.
	 * @return object
	 */
	public function unserialize( $item, $data ) {
		$object_access = new ObjectAccess( $data );

		$reflection_object = new ReflectionObject( $item );

		$properties = $reflection_object->getProperties();

		foreach ( $properties as $property ) {
			$attributes = $property->getAttributes( RemoteApiProperty::class );

			foreach ( $attributes as $attribute ) {
				$remote_api_property = $attribute->newInstance();

				if ( $object_access->has_property( $remote_api_property->name ) ) {
					$property->setValue( $item, $object_access->get_property( $remote_api_property->name ) );
				}
			}
		}

		return $item;
	}
}
