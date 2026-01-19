<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Exception;

use Throwable;

final class NetworkException extends \Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
