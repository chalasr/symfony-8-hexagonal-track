<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Validation;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\BookName;
use Symfony\Component\Validator\Attribute\ExtendsValidationFor;
use Symfony\Component\Validator\Constraints as Assert;

/** Slide 24 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=24 */
#[ExtendsValidationFor(Book::class)]
final class BookValidation
{
    #[Assert\NotBlank]                                  // mirrors InvalidBookNameException::blank
    #[Assert\Length(min: 1, max: BookName::MAX_LENGTH)] // mirrors InvalidBookNameException::tooLong
    public string $name;

    #[Assert\PositiveOrZero]                            // mirrors InvalidPriceException::negative
    public int $price;
}
