<?php declare(strict_types=1);

return [
	'prefix' => null,
	'finders' => [],
	'patchers' => [
		function (string $filePath, string $prefix, string $content): string {
			if ($filePath !== 'bin/phpstan') {
				return $content;
			}
			return str_replace('__DIR__ . \'/..', '\'phar://phpstan.phar', $content);
		},
	],
	'whitelist' => [
		'PHPStan\*',
		'PHPUnit\Framework\TestCase',
	],
];
