<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Validation;

use App\BookStore\Domain\Model\Book;
use Symfony\Component\Validator\Attribute\ExtendsValidationFor;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slide 24: validation metadata for `Book` lives OUTSIDE the domain.
 * `#[ExtendsValidationFor]` (Sf 7.4) attaches these constraints to `Book::class`
 * without polluting the domain class with `#[Assert\*]` attributes.
 */
#[ExtendsValidationFor(Book::class)]
final class BookValidation
{
    #[Assert\Length(min: 1, max: 255)]
    public string $name;

    #[Assert\PositiveOrZero]
    public int $price;
}
