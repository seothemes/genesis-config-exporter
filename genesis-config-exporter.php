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

/**
 * Autoload classes.
 *
 * @noinspection PhpUnhandledExceptionInspection
 */
\spl_autoload_register( function ( $class ) {
	if ( strpos( $class, __NAMESPACE__ ) !== false ) {
		require_once __DIR__ . '/src' . str_replace( '\\', DIRECTORY_SEPARATOR, substr( $class, strlen( __NAMESPACE__ ) ) ) . '.php';
	}
} );

// Hook everything to after setup theme.
\add_action( 'after_setup_theme', function () {
	$command = new Command( new Generator( new Plugin( __FILE__ ) ) );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {

		/**
		 * Add WP CLI command.
		 *
		 * @var callable $command __invoke magic method.
		 */
		\WP_CLI::add_command( 'genesis config', $command );
	}
}, 20 );
