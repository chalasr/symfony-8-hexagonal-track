<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Domain\ValueObject;

use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Domain\Exception\InvalidIdentifierException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class BookIdTest extends TestCase
{
    public function testItGeneratesUuidV4WhenOmitted(): void
    {
        $id = new BookId();

        self::assertInstanceOf(Uuid::class, $id->value);
        self::assertTrue(Uuid::isValid((string) $id));
    }

    public function testItAcceptsUuidInstance(): void
    {
        $uuid = Uuid::v4();

        self::assertSame($uuid, new BookId($uuid)->value);
    }

    public function testItAcceptsUuidString(): void
    {
        $uuid = Uuid::v4();

        self::assertTrue(new BookId((string) $uuid)->value->equals($uuid));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidStrings(): iterable
    {
        yield 'empty' => [''];
        yield 'not a uuid' => ['not-a-uuid'];
        yield 'almost a uuid' => ['12345678-1234-1234-1234-12345678901Z'];
    }

    #[DataProvider('invalidStrings')]
    public function testItRefusesNonUuidString(string $value): void
    {
        $this->expectException(InvalidIdentifierException::class);
        $this->expectExceptionMessageMatches('/is not a valid UUID/');

        new BookId($value);
    }
}
