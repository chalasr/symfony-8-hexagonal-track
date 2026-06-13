<?php

declare(strict_types=1);

namespace App\BookStore\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/** Slide 23 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=23 */
final class CheapestBooksFilter
{
    public function __construct(
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $size = 10,
    ) {
    }
}
