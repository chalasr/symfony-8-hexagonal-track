<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Domain\ValueObject;

use App\BookStore\Domain\Exception\InvalidPriceException;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    /**
     * @return iterable<string, array{int}>
     */
    public static function validAmounts(): iterable
    {
        yield 'zero' => [0];
        yield 'one' => [1];
        yield 'ten thousand' => [10_000];
        yield 'large' => [1_000_000];
    }

    #[DataProvider('validAmounts')]
    public function testItAcceptsNonNegativeAmount(int $amount): void
    {
        self::assertSame($amount, new Price($amount)->amount);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function invalidAmounts(): iterable
    {
        yield 'minus one' => [-1];
        yield 'large negative' => [-1_000_000];
    }

    #[DataProvider('invalidAmounts')]
    public function testItRefusesNegativeAmount(int $amount): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->expectExceptionMessageMatches('/Price amount must be >= 0/');

        new Price($amount);
    }

    public function testApplyDiscountReturnsNewInstanceWithReducedAmount(): void
    {
        $price = new Price(10_000);
        $discounted = $price->applyDiscount(new Discount(25));

        self::assertSame(10_000, $price->amount);
        self::assertSame(7_500, $discounted->amount);
    }

    public function testApplyMaxDiscountYieldsZero(): void
    {
        $discounted = new Price(10_000)->applyDiscount(new Discount(100));

        self::assertSame(0, $discounted->amount);
    }
}
