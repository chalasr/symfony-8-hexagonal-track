<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 * The pristine aggregate (slide 11): no Validator, no Serializer, no Groups.
 * PHP 8.4 asymmetric visibility (slide 13): read everywhere, write inside.
 * Doctrine mapping attributes are kept on purpose (slide 29: object-pure violation).
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

    /**
     * PHP 8.4 property hook (slide 14): computed property, no anemic getter.
     */
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
