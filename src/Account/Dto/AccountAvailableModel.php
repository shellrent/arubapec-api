<?php

declare(strict_types=1);

namespace Shellrent\Arubapec\Account\Dto;

use Shellrent\Arubapec\Exception\UnexpectedResponseException;

final class AccountAvailableModel
{
    public function __construct(
        private readonly ?bool $accountExists,
        private readonly ?bool $isAccountAssignable
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $accountExists = null;
        $isAccountAssignable = null;

        if (array_key_exists('accountExists', $payload)) {
            if (!is_bool($payload['accountExists'])) {
                throw new UnexpectedResponseException('Invalid accountExists flag.');
            }

            $accountExists = $payload['accountExists'];
        }

        if (array_key_exists('isAccountAssignable', $payload)) {
            if (!is_bool($payload['isAccountAssignable'])) {
                throw new UnexpectedResponseException('Invalid isAccountAssignable flag.');
            }

            $isAccountAssignable = $payload['isAccountAssignable'];
        }

        return new self($accountExists, $isAccountAssignable);
    }

    public function getAccountExists(): ?bool
    {
        return $this->accountExists;
    }

    public function isAccountAssignable(): ?bool
    {
        return $this->isAccountAssignable;
    }
}
