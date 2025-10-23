<?php
/**
 * Note
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Stringable;

/**
 * Note class
 */
final class Note implements RemoteSerializable, Stringable{
	/**
	 * Note.
	 *
	 * @var string
	 */
	#[RemoteApiProperty( 'note' )]
	public $note;

	/**
	 * Todo.
	 *
	 * @var bool|null
	 */
	#[RemoteApiProperty( 'todo' )]
	public $todo;

	/**
	 * Assignee ID.
	 *
	 * @var string|null
	 */
	#[RemoteApiProperty( 'assignee_id' )]
	public $assignee_id;

	/**
	 * Construct note.
	 *
	 * @param string $note Note.
	 */
	public function __construct( string $note ) {
		$this->note = $note;
	}

	/**
	 * Remote serialize.
	 *
	 * @link https://developer.moneybird.com/api/financial_statements/#post_financial_statements
	 * @link https://www.php.net/manual/en/language.attributes.overview.php#127899
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		$serializer = new RemoteSerializer( $context );

		return $serializer->serialize( $this );
	}

	/**
	 * To string.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->note;
	}
}
