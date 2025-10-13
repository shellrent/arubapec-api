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
use Shellrent\Arubapec\AdditionalService\Dto\AdditionalServiceCreateRequest;
use Shellrent\Arubapec\Domain\Dto\DomainByNameRequest;
use Shellrent\Arubapec\Domain\Dto\DomainInfoRequest;
use Shellrent\Arubapec\Domain\Dto\DomainSearchRequest;
use Shellrent\Arubapec\Shared\Dto\RenewalData;
use Shellrent\Arubapec\Shared\Dto\PageRequestOptions;

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

$additionalService = $client->additionalService()->create(new AdditionalServiceCreateRequest(
    'pec@example.com',
    'NEWSLETTER',
    new RenewalData('T', 1)
));

if ($service = $additionalService->getData()) {
    printf('Additional service %d is %s', $service->getId(), $service->getStatus());
}

// Retrieve the country catalogue used by various onboarding workflows
$countries = $client->country()->countries();

foreach ($countries->getData() as $country) {
    printf("Country #%d: %s\n", $country->getId(), $country->getName());
}

// Inspect an existing domain and verify whether it is certifiable
$domainInfo = $client->domain()->info(new DomainInfoRequest(fullName: 'pec.example.com', loadExtraData: true));

if ($domain = $domainInfo->getData()) {
    printf('Domain %s expires on %s', $domain->getFullName(), $domain->getEndDate()->toRfc3339String());
}

$canBeCertified = $client->domain()->verifyCertifiability(new DomainByNameRequest('new-domain.example.com'));

if ($canBeCertified->getData() === true) {
    echo 'The domain can be certified.';
}

// Search domains by status with pagination helpers shared across modules
$search = $client->domain()->search(
    new DomainSearchRequest(status: 'CERTIFICATO'),
    new PageRequestOptions(page: 0, size: 20, sort: ['fullName,asc'])
);

if (($page = $search->getData()) !== null) {
    foreach ($page->getContent() as $item) {
        printf("Found domain %s owned by %s %s\n", $item->getFullName(), $item->getOwner()->getName(), $item->getOwner()->getSurname());
    }
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
