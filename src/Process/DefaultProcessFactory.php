<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Process;

use Symfony\Component\Console\Output\OutputInterface;

final class DefaultProcessFactory implements ProcessFactory
{

	public function create(string $command, string $cwd, OutputInterface $output): Process
	{
		return new SymfonyProcess($command, $cwd, $output);
	}

}
