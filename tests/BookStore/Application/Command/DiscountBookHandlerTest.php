<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Application\Command;

use App\BookStore\Application\Command\DiscountBookCommand;
use App\BookStore\Application\Command\DiscountBookHandler;
use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use PHPUnit\Framework\TestCase;

final class DiscountBookHandlerTest extends TestCase
{
    public function testItAppliesDiscount(): void
    {
        $books = new InMemoryBookRepository();
        $book = new Book(new BookName('Domain-Driven Design'), new Price(10000));
        $books->add($book);

        $handler = new DiscountBookHandler($books);
        $handler(new DiscountBookCommand($book->id, new Discount(25)));

        self::assertSame(7500, $books->ofId($book->id)->price->amount);
    }

    public function testItThrowsWhenBookIsMissing(): void
    {
        $books = new InMemoryBookRepository();
        $book = new Book(new BookName('Unknown'), new Price(1000));

        $this->expectException(MissingBookException::class);

        (new DiscountBookHandler($books))(new DiscountBookCommand($book->id, new Discount(10)));
    }
}
