<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 * Slides 11, 13 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=11
 *
 * Doctrine mapping attributes here are an intentional dependency-rule violation
 * (slide 29, https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=29):
 * metadata-only, no IO at construct time. Don't "fix" them away.
 */
#[ORM\Entity]
class Book
{
    #[ORM\Embedded(columnPrefix: false)]
    public private(set) BookId $id;

    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        public private(set) BookName $name,

        #[ORM\Embedded(columnPrefix: false)]
        public private(set) Price $price,
    ) {
        $this->id = new BookId();
    }

    /** Slide 14 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=14 */
    public string $displayName {
        get => $this->name->value;
    }

    public function rename(BookName $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function applyDiscount(Discount $discount): static
    {
        $this->price = $this->price->applyDiscount($discount);

        return $this;
    }
}
