# Genesis Config Exporter

Automates the min-numbingly tedious task of writing onboarding config files for Genesis child themes.

Below is a list of configs that can be automatically generated:

- **Plugins** - All active plugins. Deactivated plugins are skipped.
- **Content** - All post meta and content. Can be limited by post type.
- **Images** - Only images that are set as featured images are included.
- **Menus** - All registered navigation menus and menu items.
- **Widgets** - All active widgets. Excludes Inactive Widgets.

## Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | 7.2.0   |
| WordPress   | 5.2.3   |
| Genesis     | 3.1.2   |
| WP CLI      | 2.1.0   |

## Installation

This is a regular plugin. Install it as you would any other.

You will also need WP CLI installed on your machine to use it. See [installing WP-CLI](https://wp-cli.org/#installing).

## Usage

From the command line, navigate to anywhere in your WordPress installation and run the following command:

```shell
wp genesis config export
```

When you see the following prompt, hit `y` and then `return` to continue:

```shell
Warning: This will overwrite your current child theme's onboarding config file. Proceed? [y/n]
```

### Args

#### dry

Does not generate the file:

```shell
--dry=true
```

#### force

Disables the warning prompt:

```shell
--force=true
```

### Extra

#### Debugging

A debugging class is conveniently provided to allow quick debugging in the browser. To enable, open the `src/Debug.php` file in your editor and change the DEBUG const from false to true. WP_DEBUG also needs to be set to true to enable debugging.

#### Admin Settings

Coming soon.

## Contributing

**Important:** Please run `composer dump --no-dev` before committing changes.
