<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Replacer
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Replacer {

	/**
	 * @var string
	 */
	public $template;

	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Replacer constructor.
	 *
	 * @param string $template
	 * @param Plugin $plugin
	 */
	public function __construct( string $template, Plugin $plugin ) {
		$this->template = $template;
		$this->plugin   = $plugin;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function replace() {
		$template = $this->template;
		$theme    = \wp_get_theme();

		$replace = [
			'name'    => $theme->get( 'Name' ),
			'package' => $theme->get( 'Name' ),
			'author'  => $theme->get( 'Author' ),
			'link'    => $theme->get( 'ThemeURI' ),
			'plugins' => $this->get_plugins(),
			'content' => $this->get_content(),
			'navmenu' => $this->get_navmenu(),
			'widgets' => $this->get_widgets(),
		];

		foreach ( $replace as $from => $to ) {
			$template = str_replace( "{{{$from}}}", $to, $template );
		}

		return $template;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_plugins() {
		$plugins = '';
		$active  = \array_values( \get_option( 'active_plugins' ) );

		if ( ( $key = array_search( $this->plugin->base, $active ) ) !== false ) {
			unset( $active[ $key ] );
		}

		foreach ( $active as $slug ) {
			$base = explode( DIRECTORY_SEPARATOR, $slug )[0];
			$name = ucwords( str_replace( '-', ' ', $base ) );
			$url  = "https://wordpress.org/plugins/$base/";

			$plugins .= "[
				'name' => '{$name}',
				'slug' => '{$slug}',
				'public_url' => '{$url}',
			],\n";
		}

		return $plugins;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_content() {
		$content = '';
		$posts   = \get_posts( [
			'numberposts' => -1,
			'post_type'   => 'any',
		] );

		foreach ( $posts as $post ) {
			$meta_input   = '';
			$meta         = \get_post_meta( $post->ID, false, true );
			$image        = [
				'dir'  => \get_stylesheet_directory() . '/config/import/images',
				'file' => '',
				'path' => '',
				'url'  => '',
			];
			$post_content = \str_replace( '\'', '&apos;', $post->post_content );
			$post_content = "\n\t\t\t'post_content'   => '{$post_content}',";

			$featured_image = '';

			foreach ( $meta as $key => $value ) {
				if ( isset( $value[0] ) && ! empty( $value[0] ) ) {
					$meta_input .= "\n\t\t\t\t'{$key}' => '{$value[0]}',";
				}
			}

			$meta_input = ! empty( $meta_input ) ? "\n\t\t\t'meta_input'     => [ $meta_input \n\t\t\t\t]," : '';

			if ( isset( $meta['_thumbnail_id'][0] ) ) {
				$image['path'] = \get_attached_file( $meta['_thumbnail_id'][0] );
				$image['name'] = \basename( $image['path'] );
				$image['file'] = $image['dir'] . DIRECTORY_SEPARATOR . $image['name'];
				$image['url']  = "\\get_stylesheet_directory() . '/config/import/images/{$image['name']}'";

				// Create directory.
				if ( ! is_dir( $image['dir'] ) ) {
					\wp_mkdir_p( $image['dir'] );
				}

				if ( ! file_exists( $image['file'] ) ) {
					\copy( $image['path'], $image['file'] );
				}

				$featured_image = "\n\t\t\t'featured_image' => {$image['url']},";
			}

			$content .= "'{$post->post_name}' => [
			'post_title'     => '{$post->post_title}',
			'post_type'      => '{$post->post_type}',
			'post_status'    => '{$post->post_status}',
			'comment_status' => '{$post->comment_status}',
			'ping_status'    => '{$post->ping_status}',";

			$content .= $featured_image . $meta_input . $post_content;
			$content .= "\n\t\t],\n\t\t";
		}

		return $content;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_navmenu() {
		$menus     = '';
		$locations = \get_registered_nav_menus();

		foreach ( $locations as $location => $title ) {
			$menu_items = \wp_get_nav_menu_items( $title );

			if ( ! is_array( $menu_items ) ) {
				continue;
			}

			$menus .= "'$location' => [\n";

			foreach ( $menu_items as $menu_item ) {
				$url = \basename( $menu_item->url );

				$menu_item_slugs[ $menu_item->ID ] = $url;

				$menus .= "\t\t\t'{$url}' => [\n";
				$menus .= "\t\t\t\t'title' => '{$menu_item->title}',\n";
				$menus .= "\t\t\t\t'id'    => '{$url}',\n";

				// If has a parent.
				if ( $menu_item->menu_item_parent ) {
					$menus .= "\t\t\t\t'parent' => '{$menu_item_slugs[$menu_item->menu_item_parent]}',\n";
				}

				$menus .= "\t\t\t],\n";
			}

			$menus .= "\t\t],";
		}

		return $menus;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_widgets() {
		$widgets = '';

		$sidebars_widgets = \wp_get_sidebars_widgets();
		unset( $sidebars_widgets['wp_inactive_widgets'] );

		foreach ( $sidebars_widgets as $widget_area => $widgets_array ) {

			if ( empty( $widgets_array ) ) {
				continue;
			}

			$widgets .= "\t\t'$widget_area' => [\n";

			foreach ( $widgets_array as $widget ) {
				$type = substr( $widget, 0, strrpos( $widget, '-' ) );
				$id   = substr( $widget, strrpos( $widget, '-' ) + 1 );
				$args = \get_option( "widget_$type" )[ $id ];

				$widgets .= "\t\t\t[\n";
				$widgets .= "\t\t\t\t'type' => '$type',\n";

				if ( ! empty( $args ) ) {
					$widgets .= "\t\t\t\t'args' => [\n";

					foreach ( $args as $key => $value ) {
						$escaped = \str_replace( '\'', '&apos;', $value );
						$widgets .= "\t\t\t\t\t'$key' => '$escaped',\n";
					}

					$widgets .= "\t\t\t\t],";
				}

				$widgets .= "\n\t\t\t],";
			}

			$widgets .= "\n\t\t],\n";
		}

		return $widgets;
	}

}
