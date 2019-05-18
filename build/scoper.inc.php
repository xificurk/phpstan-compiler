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
			if (substr($filePath, 0, 23) !== 'vendor/nette/di/src/DI/') {
				return $content;
			}
			return str_replace(
				'|Nette\\\\DI\\\\Definitions\\\\Statement',
				sprintf('|\\\\%s\\\\Nette\\\\DI\\\\Definitions\\\\Statement', $prefix),
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 23) !== 'vendor/nette/di/src/DI/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(string|int|float|null|callable)~",
				'$1',
				$content
			);
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
			if ($filePath !== 'src/Reflection/SignatureMap/functionMap.php') {
				return $content;
			}
			return preg_replace("~$prefix\\\\{1,2}~", '', $content);
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
				'~(PhpParser|Pepakriz)(\\\\{1,2})~',
				"$prefix$2$1$2",
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 32) !== 'vendor/phpstan/phpstan-doctrine/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(Doctrine)~",
				'$1',
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 28) !== 'vendor/phpstan/phpstan-dibi/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(Dibi)~",
				'$1',
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 29) !== 'vendor/phpstan/phpstan-nette/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(Nette|Latte|Tracy)~",
				'$1',
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 31) !== 'vendor/phpstan/phpstan-phpunit/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(PHPUnit)~",
				'$1',
				$content
			);
		},
		function (string $filePath, string $prefix, string $content): string {
			if (substr($filePath, 0, 31) !== 'vendor/phpstan/phpstan-mockery/') {
				return $content;
			}
			return preg_replace(
				"~$prefix\\\\{1,2}(Mockery)~",
				'$1',
				$content
			);
		},
	],
	'whitelist' => [
		'PHPStan\*',
	],
];
