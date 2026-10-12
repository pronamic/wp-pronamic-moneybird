<?php
/**
 * Rector
 *
 * @package   Pronamic\Moneybird
 * @author    Pronamic
 * @copyright 2024 Pronamic
 * @license   GPL-2.0-or-later
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
	->withPaths(
		[
			__DIR__ . '/src',
		]
	)
	->withPhpSets()
	->withTypeCoverageLevel( 0 )
	->withDeadCodeLevel( 0 )
	->withCodeQualityLevel( 0 );
