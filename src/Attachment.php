<?php
/**
 * Attachment
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Attachment class
 */
final class Attachment {
	public $filename;

	public $contents;

	public $type;

	public function __construct( $filename, $contents, $type ) {
		$this->filename = $filename;
		$this->contents = $contents;
		$this->type     = $type;
	}
}
