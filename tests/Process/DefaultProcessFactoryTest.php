<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Process;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

final class DefaultProcessFactoryTest extends TestCase
{

	public function testCreate(): void
	{
		$output = $this->createMock(OutputInterface::class);
		$output->expects(self::once())->method('write');

		$process = (new DefaultProcessFactory())->create('ls', __DIR__, $output)->getProcess();
		self::assertSame('ls', $process->getCommandLine());
		self::assertSame(__DIR__, $process->getWorkingDirectory());
	}

}
