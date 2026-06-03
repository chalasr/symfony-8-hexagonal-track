<?php

declare(strict_types=1);

namespace App\BookStore\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Target of `#[MapQueryString]` on slide 23.
 */
final class CheapestBooksFilter
{
    public function __construct(
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $size = 10,
    ) {
    }
}
