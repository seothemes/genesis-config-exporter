<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Admin
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Admin {

	/**
	 * @var bool
	 */
	const ADMIN_WORKING = false;

	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * Admin constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin, Generator $generator ) {
		$this->plugin    = $plugin;
		$this->generator = $generator;
	}

	/**
	 * Register admin hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		if ( ! \current_user_can( 'manage_options' ) ) {
			return;
		}

		// TODO: Remove when admin functionality is working.
		if ( ! self::ADMIN_WORKING ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin->file ), [ $this, 'action_links' ] );
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_submenu_page(
			'genesis',
			$this->plugin->name,
			'Export Config',
			'manage_options',
			$this->plugin->handle,
			[ $this, 'settings_page' ]
		);
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		$id   = $this->plugin->handle;
		$tab  = $this->plugin->handle;
		$page = $this->plugin->handle;

		add_settings_section( $id, $tab, '', $page );
		add_settings_field( $id, $id, [ $this, 'display_field' ], $page, $id, $id );
		register_setting( $page, $id );
	}

	/**
	 * Display settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( $this->plugin->name ); ?></h1>

			<p class="submit">
				<a href="<?php echo \get_stylesheet_directory_uri(); ?>/config/template.php" class="button button-primary" download>Download
					Onboarding Config</a>
			</p>

		</div>
		<?php
	}

	/**
	 * Add settings link to plugins list.
	 *
	 * @since 1.0.0
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( $links, [
			sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=' . $this->plugin->handle ),
				__( 'Settings', 'genesis-code-snippets' ) ),
		] );
	}

}
