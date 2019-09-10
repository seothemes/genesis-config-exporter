<?php

namespace SeoThemes\GenesisConfigExporter\Tests;

use Brain\Monkey;

/**
 * Class TestCase
 *
 * @package \SeoThemes\GenesisConfigExporter\Tests
 */
class TestCase extends \PHPUnit\Framework\TestCase {

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		Monkey\setUp();

		parent::setUp();
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();

		parent::tearDown();
	}
}
