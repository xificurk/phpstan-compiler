# PHAR Compiler for PHPStan

This is a fork of the official [phpstan-compiler](https://github.com/phpstan/phpstan-compiler) with 2 main differences:
1) It prefixes `PhpParser` namespace.
2) It bundles several phpstan extensions into the phar distribution.

## Installation

```bash
git clone https://github.com/xificurk/phpstan-compiler
cd phpstan-compiler
composer install
```

## Compile the PHAR

```bash
php bin/compile [version] [repository]
```

Default `version` is `master`, and default `repository` is `https://github.com/phpstan/phpstan.git`.

The compiled PHAR will be in `tmp/phpstan.phar`.
