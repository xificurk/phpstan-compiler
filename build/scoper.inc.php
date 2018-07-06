<?php declare(strict_types=1);

return [
	'prefix' => 'PHPStanVendor',
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
			if ($filePath !== 'src/Testing/TestCase.php') {
				return $content;
			}
			return str_replace(sprintf('\\%s\\PHPUnit\\Framework\\TestCase', $prefix), '\\PHPUnit\\Framework\\TestCase', $content);
		},
		function (string $filePath, string $prefix, string $content): string {
			if ($filePath !== 'src/Testing/LevelsTestCase.php') {
				return $content;
			}
			return str_replace(sprintf('\\%s\\PHPUnit\\Framework\\AssertionFailedError', $prefix), '\\PHPUnit\\Framework\\AssertionFailedError', $content);
		},
		function (string $filePath, string $prefix, string $content): string {
			if ($filePath !== 'vendor/nikic/php-parser/lib/PhpParser/NodeAbstract.php') {
				return $content;
			}
			$length = 15 + strlen($prefix) + 1;
			return preg_replace(
				'~strpos\((.+?)\) \+ 15~',
				sprintf('strpos($1) + %d', $length),
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, -5) !== '.neon') {
				return $content;
			}
			return preg_replace(
				'~PhpParser\\\\~',
				"$prefix\\PhpParser\\",
				$content
			);
		},
	],
	'whitelist' => [
		'PHPStan\*',
	],
];
