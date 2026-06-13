<?php

declare(strict_types=1);

namespace App\BookStore\Application\Dto;

use App\BookStore\Domain\Model\Book;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;

/** Slide 25 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=25 */
#[Map(source: Book::class)]
final class BookResource
{
    public function __construct(
        #[Groups(['book:read'])]
        #[Map(source: 'id', transform: [self::class, 'idToString'])]
        public readonly string $id,

        #[Groups(['book:read'])]
        #[Map(source: 'name', transform: [self::class, 'nameToString'])]
        public readonly string $name,

        #[Groups(['book:read'])]
        #[Map(source: 'price', transform: [self::class, 'priceToInt'])]
        public readonly int $price,
    ) {
    }

    public static function idToString(mixed $id): string
    {
        return (string) $id;
    }

    public static function nameToString(mixed $name): string
    {
        return $name->value;
    }

    public static function priceToInt(mixed $price): int
    {
        return $price->amount;
    }
}
