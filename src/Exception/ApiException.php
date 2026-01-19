<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Exception;

use Shellrent\Arubapec\Shared\Dto\RestErrorResponse;
use Throwable;

final class ApiException extends \Exception
{
    private readonly int $statusCode;

    private readonly ?RestErrorResponse $errorResponse;

    public function __construct(
        string $message,
        int $statusCode,
        ?RestErrorResponse $errorResponse = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errorResponse = $errorResponse;
    }

    public static function fromErrorResponse(int $statusCode, RestErrorResponse $errorResponse): self
    {
        $message = 'ArubaPEC API responded with an error.';

        if ($errorResponse->getFirstErrorDescription() !== null) {
            $message = $errorResponse->getFirstErrorDescription();
        }

        return new self($message, $statusCode, $errorResponse);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorResponse(): ?RestErrorResponse
    {
        return $this->errorResponse;
    }
}
