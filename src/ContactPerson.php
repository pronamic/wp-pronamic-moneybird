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
	 * First name.
	 * 
	 * @var string
	 */
	#[RemoteApiProperty( 'firstname' )]
	public $first_name;

	/**
	 * Last name.
	 * 
	 * @var string
	 */
	#[RemoteApiProperty( 'lastname' )]
	public $last_name;

	/**
	 * Construct contact person.
	 * 
	 * @param string $first_name First name.
	 * @param string $last_name  Last Name.
	 */
	public function __construct( $first_name, $last_name ) {
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
	}
}
