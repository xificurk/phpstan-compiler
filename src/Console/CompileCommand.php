<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Console;

use Nette\Utils\Json;
use PHPStan\Compiler\Filesystem\Filesystem;
use PHPStan\Compiler\Process\ProcessFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CompileCommand extends Command
{

	private const BUNDLED_PHPSTAN_EXTENSIONS = [
		'pepakriz/phpstan-exception-rules' => '*',
		'phpstan/phpstan-dibi' => '*',
		'phpstan/phpstan-doctrine' => '*',
		'phpstan/phpstan-mockery' => '*',
		'phpstan/phpstan-nette' => '*',
		'phpstan/phpstan-phpunit' => '*',
		'phpstan/phpstan-strict-rules' => '*',
	];

	/** @var Filesystem */
	private $filesystem;

	/** @var ProcessFactory */
	private $processFactory;

	/** @var string */
	private $dataDir;

	/** @var string */
	private $buildDir;

	public function __construct(
		Filesystem $filesystem,
		ProcessFactory $processFactory,
		string $dataDir,
		string $buildDir
	)
	{
		parent::__construct();
		$this->filesystem = $filesystem;
		$this->processFactory = $processFactory;
		$this->dataDir = $dataDir;
		$this->buildDir = $buildDir;
	}

	protected function configure(): void
	{
		$this->setName('phpstan:compile')
			->setDescription('Compile PHAR')
			->addArgument('version', InputArgument::OPTIONAL, 'Version (tag/commit) to compile', 'master')
			->addArgument('repository', InputArgument::OPTIONAL, 'Repository to compile', 'https://github.com/phpstan/phpstan.git');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->processFactory->setOutput($output);

		if ($this->filesystem->exists($this->buildDir)) {
			$this->filesystem->remove($this->buildDir);
		}
		$this->filesystem->mkdir($this->buildDir);

		/** @var string $repository */
		$repository = $input->getArgument('repository');
		/** @var string $version */
		$version = $input->getArgument('version');

		$this->processFactory->create(sprintf('git clone %s .', \escapeshellarg($repository)), $this->buildDir);
		$this->processFactory->create(sprintf('git checkout --force %s', \escapeshellarg($version)), $this->buildDir);
		$this->processFactory->create('composer require --no-update dg/composer-cleaner:^2.0', $this->buildDir);
		foreach (self::BUNDLED_PHPSTAN_EXTENSIONS as $extensionName => $extensionVersion) {
			$this->processFactory->create(sprintf('composer require --no-update %s:%s', $extensionName, $extensionVersion), $this->buildDir);
		}
		$this->fixComposerJson($this->buildDir);
		$this->processFactory->create('composer update --no-dev --classmap-authoritative', $this->buildDir);

		$this->processFactory->create('php box.phar compile', $this->dataDir);

		return 0;
	}

	private function fixComposerJson(string $buildDir): void
	{
		$json = Json::decode($this->filesystem->read($buildDir . '/composer.json'), Json::FORCE_ARRAY);

		// remove dev dependencies (they create conflicts)
		unset($json['require-dev'], $json['autoload-dev']);

		// simplify autoload (remove not packed build directory]
		$json['autoload']['psr-4']['PHPStan\\'] = 'src/';

		// force platform
		$json['config']['platform']['php'] = ltrim($json['require']['php'], '~');

		// keep neons from extensions
		foreach (array_keys(self::BUNDLED_PHPSTAN_EXTENSIONS) as $extensionName) {
			$json['config']['cleaner-ignore'][$extensionName] = [
				'extension.neon',
				'rules.neon',
			];
		}

		$encodedJson = Json::encode($json, Json::PRETTY);

		$this->filesystem->write($buildDir . '/composer.json', $encodedJson);
	}

}
