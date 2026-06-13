<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\Shared\Domain\Exception\InvariantViolationException;

final class InvalidBookNameException extends InvariantViolationException
{
    public static function blank(): self
    {
        return new self('Book name must not be blank.');
    }

    public static function tooLong(int $length, int $max): self
    {
        return new self(\sprintf('Book name must be at most %d characters, got %d.', $max, $length));
    }
}
