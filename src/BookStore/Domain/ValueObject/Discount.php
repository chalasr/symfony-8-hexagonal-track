<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidDiscountException;

final readonly class Discount
{
    public function __construct(
        public int $percentage,
    ) {
        if ($percentage < 1 || $percentage > 100) {
            throw InvalidDiscountException::outOfRange($percentage);
        }
    }
}
