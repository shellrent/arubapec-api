# Shellrent ArubaPEC API PHP Client

Official PHP library by Shellrent to interact with the ArubaPEC APIs.

## Requirements

- PHP >= 8.1
- Common PHP extensions for web applications (curl, json, mbstring, etc.)
- [Composer](https://getcomposer.org/) for dependency management

## Installation

The package will be published on Packagist as `shellrent/arubapec-api`.
In the meantime, you can install it directly from the repository:

```bash
composer require shellrent/arubapec-api:dev-main
```

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Shellrent\Arubapec\ArubapecClient;

$client = new ArubapecClient();

// TODO: configure credentials and use the client methods
```

## Development workflow

- `src/`: library source code
- `tests/`: automated tests (PHPUnit)
- `openapi/`: official ArubaPEC specifications in OpenAPI format

### Coding standards

The project uses [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) to enforce coding style.

```bash
composer lint       # dry-run style check
composer lint:fix   # automatically fix coding style issues
```

### Testing

Run the test suite with:

```bash
composer install
composer test
```

## Contributing

1. Fork the repository
2. Create a feature branch for your change
3. Open a pull request with a detailed description

## License

Distributed under the [MIT](LICENSE) license.
