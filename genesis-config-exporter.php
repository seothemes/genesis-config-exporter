<?php
/**
 * Plugin Name: Genesis Config Exporter
 * Plugin URI:  https://github.com/seothemes/genesis-config-exporter
 * Description: WP CLI command to export config file for child theme.
 * Version:     1.0.0
 * Author:      SEO Themes
 * Author URI:  https://seothemes.com/
 * License:     GPL-2.0-or-later
 * Text Domain: genesis-config-exporter
 */

declare( strict_types=1 );

namespace SeoThemes\GenesisConfigExporter;

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Composer packages.
require_once __DIR__ . '/vendor/autoload.php';

// Run after theme is setup.
\add_action( 'after_setup_theme', function () {
	$injector  = new \Dice\Dice();
	$rules     = require_once __DIR__ . '/config/rules.php';
	$container = $injector->addRules( $rules );
	$command   = $container->create( Command::class );
	$admin     = $container->create( Admin::class );
	$debug     = $container->create( Debug::class );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		try {
			/**
			 * Add WP CLI command.
			 *
			 * @var callable $command __invoke magic method.
			 */
			\WP_CLI::add_command( 'genesis config', $command );

		} catch ( \Exception $exception ) {
			new \WP_Error( $exception );
		}
	} else if ( \is_admin() ) {
		$admin->register();

	} else {
		$debug->dump();
	}
}, 20 );


