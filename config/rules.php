<?php

namespace SeoThemes\GenesisConfigExporter;

return [
	'*'             => [ 'shared' => true ],
	Plugin::class   => [
		'constructParams' => [
			dirname( __DIR__ ) . '/genesis-config-exporter.php',
		],
	],
	Replacer::class => [
		'constructParams' => [
			require __DIR__ . '/template.php',
		],
	],
];
