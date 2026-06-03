<?php

declare(strict_types=1);

namespace App\BookStore\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Target of `#[MapRequestPayload]` on slide 20.
 * Validation lives here in the Application layer, not on the domain Book.
 */
final class DiscountBookPayload
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Range(min: 1, max: 100)]
        public readonly int $percentage,
    ) {
    }
}
