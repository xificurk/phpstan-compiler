# PHAR Compiler for PHPStan

## Installation

```bash
git clone https://github.com/phpstan/phpstan-compiler
cd phpstan-compiler
composer install
```

## Compile the PHAR

```bash
php bin/compile [version] [repository]
```

Default `version` is `master`, and default `repository` is `https://github.com/phpstan/phpstan.git`.

The compiled PHAR will be in `tmp/phpstan.phar`.
