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
use Shellrent\Arubapec\Auth\Dto\TokenRequest;
use Shellrent\Arubapec\Account\Dto\AccountInfoRequest;

// Optionally customise the base URI or default headers
$client = new ArubapecClient(config: [
    'base_uri' => 'https://api.pec.aruba.it',
]);

$response = $client->auth()->token(new TokenRequest(
    'username@example.com',
    'super-secret-password'
));

if ($token = $response->getData()) {
    printf('Access token: %s', $token->getAccessToken());
    printf('Expires in: %d seconds', $token->getExpiresIn());
}

if ($response->getDatetime() !== null) {
    echo 'Response datetime: ' . $response->getDatetime()->toRfc3339String();
}

$accountInfo = $client->account()->info(new AccountInfoRequest('pec@example.com'));

if ($account = $accountInfo->getData()) {
    printf('Account %s is currently %s', $account->getName(), $account->getStatus());
    printf('Renewal type: %s', $account->getRenewalData()->getType());
}
```

When the API responds with an error (HTTP status code >= 400) an
`Shellrent\Arubapec\Exception\ApiException` is thrown. You can inspect the
embedded `RestErrorResponse` to understand the cause of the failure.

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
