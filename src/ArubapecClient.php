<?php

declare(strict_types=1);

namespace Shellrent\Arubapec;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Shellrent\Arubapec\Account\AccountClient;
use Shellrent\Arubapec\AdditionalService\AdditionalServiceClient;
use Shellrent\Arubapec\Auth\AuthClient;
use Shellrent\Arubapec\Config\ClientConfig;

final class ArubapecClient
{
    private readonly ClientInterface $httpClient;

    private readonly ClientConfig $config;

    private readonly AuthClient $authClient;

    private readonly AccountClient $accountClient;

    private readonly AdditionalServiceClient $additionalServiceClient;

    public function __construct(
        ?ClientInterface $httpClient = null,
        ClientConfig|array|null $config = null
    ) {
        if (is_array($config)) {
            $config = ClientConfig::fromArray($config);
        }

        $this->config = $config ?? new ClientConfig();

        $this->httpClient = $httpClient ?? new Client($this->config->getHttpClientConfig());
        $this->authClient = new AuthClient($this->httpClient);
        $this->accountClient = new AccountClient($this->httpClient);
        $this->additionalServiceClient = new AdditionalServiceClient($this->httpClient);
    }

    public function auth(): AuthClient
    {
        return $this->authClient;
    }

    public function account(): AccountClient
    {
        return $this->accountClient;
    }

    public function additionalService(): AdditionalServiceClient
    {
        return $this->additionalServiceClient;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function getConfig(): ClientConfig
    {
        return $this->config;
    }
}
