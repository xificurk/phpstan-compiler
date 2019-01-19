<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Process;

use Symfony\Component\Console\Output\OutputInterface;

final class SymfonyProcess implements Process
{

	/** @var \Symfony\Component\Process\Process */
	private $process;

	public function __construct(string $command, string $cwd, OutputInterface $output)
	{
		$this->process = (\Symfony\Component\Process\Process::fromShellCommandline($command, $cwd, null, null, null))
			->mustRun(function (string $type, string $buffer) use ($output): void {
				$output->write($buffer);
			});
	}

	public function getProcess(): \Symfony\Component\Process\Process
	{
		return $this->process;
	}

}
