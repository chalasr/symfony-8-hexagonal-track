<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidDiscountException;
use App\BookStore\Domain\ValueObject\Discount;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DiscountTest extends TestCase
{
    /**
     * @return iterable<string, array{int}>
     */
    public static function validPercentages(): iterable
    {
        yield 'min' => [1];
        yield 'mid' => [50];
        yield 'max' => [100];
    }

    #[DataProvider('validPercentages')]
    public function testItAcceptsValidPercentage(int $percentage): void
    {
        self::assertSame($percentage, new Discount($percentage)->percentage);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function invalidPercentages(): iterable
    {
        yield 'zero (no-op rejected)' => [0];
        yield 'negative' => [-10];
        yield 'over hundred' => [101];
        yield 'very high' => [10_000];
    }

    #[DataProvider('invalidPercentages')]
    public function testItRefusesOutOfRange(int $percentage): void
    {
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessageMatches('/between 1 and 100/');

        new Discount($percentage);
    }
}
