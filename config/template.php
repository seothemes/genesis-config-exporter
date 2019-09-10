<?php

namespace SeoThemes\GenesisConfigExporter;

return <<<TEMPLATE
<?php
/**
 * {{name}}
 *
 * Onboarding config shared between Starter Packs.
 *
 * Genesis Starter Packs give you a choice of content variation when activating
 * the theme. The content below is common to all packs for this theme.
 *
 * @package {{package}}
 * @author  {{author}}
 * @link    {{link}}
 * @license GPL-2.0-or-later
 */

return [
	'dependencies' => [
		'plugins'          => [
			{{plugins}}
		],
	],
	'content'          => [
		{{content}}
	],
	'navigation_menus' => [
		{{navmenu}}
	],
	'widgets'          => [
		{{widgets}}
	],
];
TEMPLATE;
