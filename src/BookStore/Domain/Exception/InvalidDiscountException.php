<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\Shared\Domain\Exception\InvariantViolationException;

final class InvalidDiscountException extends InvariantViolationException
{
    public static function outOfRange(int $percentage): self
    {
        return new self(\sprintf('Discount percentage must be between 1 and 100, got %d.', $percentage));
    }
}
