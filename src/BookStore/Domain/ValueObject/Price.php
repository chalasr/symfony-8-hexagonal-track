<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Price
{
    public function __construct(
        #[ORM\Column(name: 'price', type: 'integer', options: ['unsigned' => true])]
        public int $amount,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException(\sprintf('Price must be >= 0, got %d.', $amount));
        }
    }

    public function applyDiscount(Discount $discount): static
    {
        $amount = (int) ($this->amount - ($this->amount * $discount->percentage / 100));

        return new static($amount);
    }
}
