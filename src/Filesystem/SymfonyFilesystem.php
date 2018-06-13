<?php declare(strict_types = 1);

namespace PHPStan\Compiler\Filesystem;

final class SymfonyFilesystem implements Filesystem
{

	/** @var \Symfony\Component\Filesystem\Filesystem */
	private $filesystem;

	public function __construct(\Symfony\Component\Filesystem\Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	public function exists(string $dir): bool
	{
		return $this->filesystem->exists($dir);
	}

	public function remove(string $dir): void
	{
		$this->filesystem->remove($dir);
	}

	public function mkdir(string $dir): void
	{
		$this->filesystem->mkdir($dir);
	}

}
