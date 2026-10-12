<?php
/**
 * Contact person
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;
use ReflectionObject;

/**
 * Contact person class
 */
final class ContactPerson implements RemoteSerializable {
	/**
	 * ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'id' )]
	public $id;

	/**
	 * Construct contact person.
	 * 
	 * @param string $first_name First name.
	 * @param string $last_name  Last Name.
	 */
	public function __construct(
		/**
		 * First name.
		 */
		#[RemoteApiProperty( 'firstname' )]
		public $first_name,
		/**
		 * Last name.
		 */
		#[RemoteApiProperty( 'lastname' )]
		public $last_name
	) {
	}

	/**
	 * Remote serialize.
	 *
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		$serializer = new RemoteSerializer( $context );

		return $serializer->serialize( $this );
	}
}
