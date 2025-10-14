<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Config;

final class ClientConfig
{
    private const DEFAULT_BASE_URI = 'https://api.pec.aruba.it';

    /**
     * @param array<string, string> $defaultHeaders
     * @param array<string, mixed>  $guzzleOptions
     */
    public function __construct(
        private readonly string $baseUri = self::DEFAULT_BASE_URI,
        private readonly array $defaultHeaders = [],
        private readonly array $guzzleOptions = []
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        $baseUri = isset($config['base_uri']) ? (string) $config['base_uri'] : self::DEFAULT_BASE_URI;
        $headers = isset($config['headers']) && is_array($config['headers']) ? $config['headers'] : [];
        $guzzle = isset($config['guzzle']) && is_array($config['guzzle']) ? $config['guzzle'] : [];

        return new self($baseUri, $headers, $guzzle);
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return array<string, string>
     */
    public function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    /**
     * @return array<string, mixed>
     */
    public function getGuzzleOptions(): array
    {
        return $this->guzzleOptions;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHttpClientConfig(): array
    {
        $options = $this->guzzleOptions;

        if (isset($options['headers']) && is_array($options['headers'])) {
            $headers = $options['headers'];
            unset($options['headers']);
        } else {
            $headers = [];
        }

        $options['base_uri'] = $this->baseUri;
        $options['http_errors'] = $options['http_errors'] ?? false;

        $options['headers'] = array_merge(
            ['Accept' => 'application/json'],
            $this->defaultHeaders,
            $headers
        );

        return $options;
    }
}
