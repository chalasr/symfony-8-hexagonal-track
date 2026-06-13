<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidPriceException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Price
{
    public function __construct(
        #[ORM\Column(name: 'price', type: 'integer', options: ['unsigned' => true])]
        public int $amount,
    ) {
        if ($amount < 0) {
            throw InvalidPriceException::negative($amount);
        }
    }

    public function applyDiscount(Discount $discount): self
    {
        return new self((int) ($this->amount - ($this->amount * $discount->percentage / 100)));
    }
}
