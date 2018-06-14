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
		function (string $filePath, string $prefix, string $content): string {
			if ($filePath !== 'vendor/nette/di/src/DI/Compiler.php') {
				return $content;
			}
			return str_replace('|Nette\\\\DI\\\\Statement', sprintf('|\\\\%s\\\\Nette\\\\DI\\\\Statement', $prefix), $content);
		},
		function (string $filePath, string $prefix, string $content): string {
			if ($filePath !== 'conf/config.neon') {
				return $content;
			}
			return str_replace('PhpParser\\', sprintf('%s\\PhpParser\\', $prefix), $content);
		}
	],
	'whitelist' => [
		'PHPStan\*',
		'PHPUnit\Framework\TestCase',
	],
];
