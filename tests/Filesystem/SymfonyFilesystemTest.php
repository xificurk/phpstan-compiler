<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Filesystem;

use PHPUnit\Framework\TestCase;

final class SymfonyFilesystemTest extends TestCase
{

	public function testExists(): void
	{
		$inner = $this->createMock(\Symfony\Component\Filesystem\Filesystem::class);
		$inner->expects(self::once())->method('exists')->with('foo')->willReturn(true);

		self::assertTrue((new SymfonyFilesystem($inner))->exists('foo'));
	}

	public function testRemove(): void
	{
		$inner = $this->createMock(\Symfony\Component\Filesystem\Filesystem::class);
		$inner->expects(self::once())->method('remove')->with('foo')->willReturn(true);

		(new SymfonyFilesystem($inner))->remove('foo');
	}

	public function testMkdir(): void
	{
		$inner = $this->createMock(\Symfony\Component\Filesystem\Filesystem::class);
		$inner->expects(self::once())->method('mkdir')->with('foo')->willReturn(true);

		(new SymfonyFilesystem($inner))->mkdir('foo');
	}

}
