<?php

declare(strict_types=1);

namespace App\BookStore\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/** Slide 20 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=20 */
final class DiscountBookPayload
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Range(min: 1, max: 100)]
        public readonly int $percentage,
    ) {
    }
}
