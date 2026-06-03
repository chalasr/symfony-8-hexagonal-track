<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class BookName
{
    public function __construct(
        #[ORM\Column(name: 'name', length: 255)]
        public string $value,
    ) {
        $length = \strlen($value);
        if ($length < 1 || $length > 255) {
            throw new \InvalidArgumentException(\sprintf('BookName must be between 1 and 255 characters, got %d.', $length));
        }
    }
}
