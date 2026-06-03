<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

final readonly class Discount
{
    public function __construct(
        public int $percentage,
    ) {
        if ($percentage < 0 || $percentage > 100) {
            throw new \InvalidArgumentException(\sprintf('Discount percentage must be 0-100, got %d.', $percentage));
        }
    }
}
