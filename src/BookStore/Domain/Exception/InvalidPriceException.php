<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\Shared\Domain\Exception\InvariantViolationException;

final class InvalidPriceException extends InvariantViolationException
{
    public static function negative(int $amount): self
    {
        return new self(\sprintf('Price amount must be >= 0, got %d.', $amount));
    }
}
