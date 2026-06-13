<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidIdentifierException extends InvariantViolationException
{
    public static function notAUuid(string $value): self
    {
        return new self(\sprintf('"%s" is not a valid UUID.', self::truncate($value)));
    }

    private static function truncate(string $value, int $length = 64): string
    {
        return mb_strlen($value) > $length ? mb_substr($value, 0, $length).'…' : $value;
    }
}
