<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidBookNameException;
use App\BookStore\Domain\ValueObject\BookName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BookNameTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function validNames(): iterable
    {
        yield 'one char' => ['A'];
        yield 'typical' => ['Domain-Driven Design'];
        yield 'utf-8' => ['日本語のタイトル'];
        yield 'exactly 255 chars' => [str_repeat('A', BookName::MAX_LENGTH)];
    }

    #[DataProvider('validNames')]
    public function testItAcceptsValidName(string $value): void
    {
        self::assertSame($value, new BookName($value)->value);
    }

    public function testItRefusesEmptyString(): void
    {
        $this->expectException(InvalidBookNameException::class);
        $this->expectExceptionMessage('Book name must not be blank.');

        new BookName('');
    }

    public function testItRefusesWhitespaceOnly(): void
    {
        $this->expectException(InvalidBookNameException::class);
        $this->expectExceptionMessage('Book name must not be blank.');

        new BookName("   \t\n");
    }

    public function testItRefusesTooLong(): void
    {
        $this->expectException(InvalidBookNameException::class);
        $this->expectExceptionMessageMatches('/at most 255 characters, got 256/');

        new BookName(str_repeat('A', BookName::MAX_LENGTH + 1));
    }
}
