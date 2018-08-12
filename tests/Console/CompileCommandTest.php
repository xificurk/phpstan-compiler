<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Console;

use PHPStan\Compiler\Filesystem\Filesystem;
use PHPStan\Compiler\Process\Process;
use PHPStan\Compiler\Process\ProcessFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CompileCommandTest extends TestCase
{

	public function testCommand(): void
	{
		$filesystem = $this->createMock(Filesystem::class);
		$filesystem->expects(self::once())->method('exists')->with('bar')->willReturn(true);
		$filesystem->expects(self::once())->method('remove')->with('bar');
		$filesystem->expects(self::once())->method('mkdir')->with('bar');
		$filesystem->expects(self::once())->method('read')->with('bar/composer.json')->willReturn('{"require":{"php":"~7.1"},"require-dev":1,"autoload-dev":2,"autoload":{"psr-4":{"PHPStan\\\\":[3]}}}');
		$filesystem->expects(self::once())->method('write')->with('bar/composer.json', <<<EOT
{
    "require": {
        "php": "~7.1"
    },
    "autoload": {
        "psr-4": {
            "PHPStan\\\\": "src/"
        }
    },
    "config": {
        "platform": {
            "php": "7.1"
        },
        "cleaner-ignore": {
            "pepakriz/phpstan-exception-rules": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-dibi": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-doctrine": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-mockery": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-nette": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-phpunit": [
                "extension.neon",
                "rules.neon"
            ],
            "phpstan/phpstan-strict-rules": [
                "extension.neon",
                "rules.neon"
            ]
        }
    }
}
EOT
		);

		$process = $this->createMock(Process::class);

		$processFactory = $this->createMock(ProcessFactory::class);
		$processFactory->expects(self::at(0))->method('setOutput');
		$processFactory->expects(self::at(1))->method('create')->with('git clone \'https://github.com/phpstan/phpstan.git\' .', 'bar')->willReturn($process);
		$processFactory->expects(self::at(2))->method('create')->with('git checkout --force \'master\'', 'bar')->willReturn($process);
		$processFactory->expects(self::at(3))->method('create')->with('composer require --no-update dg/composer-cleaner:^2.0', 'bar')->willReturn($process);
		$processFactory->expects(self::at(4))->method('create')->with('composer require --no-update pepakriz/phpstan-exception-rules:~0.2.0', 'bar')->willReturn($process);
		$processFactory->expects(self::at(5))->method('create')->with('composer require --no-update phpstan/phpstan-dibi:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(6))->method('create')->with('composer require --no-update phpstan/phpstan-doctrine:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(7))->method('create')->with('composer require --no-update phpstan/phpstan-mockery:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(8))->method('create')->with('composer require --no-update phpstan/phpstan-nette:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(9))->method('create')->with('composer require --no-update phpstan/phpstan-phpunit:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(10))->method('create')->with('composer require --no-update phpstan/phpstan-strict-rules:*', 'bar')->willReturn($process);
		$processFactory->expects(self::at(11))->method('create')->with('composer update --no-dev --classmap-authoritative', 'bar')->willReturn($process);
		$processFactory->expects(self::at(12))->method('create')->with('php box.phar compile', 'foo')->willReturn($process);

		$application = new Application();
		$application->add(new CompileCommand($filesystem, $processFactory, 'foo', 'bar'));

		$command = $application->find('phpstan:compile');
		$commandTester = new CommandTester($command);
		$commandTester->execute([
			'command' => $command->getName(),
		]);

		self::assertSame('', $commandTester->getDisplay());
	}

}
