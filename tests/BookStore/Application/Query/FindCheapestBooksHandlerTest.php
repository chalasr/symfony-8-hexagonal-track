<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Application\Query;

use App\BookStore\Application\Query\FindCheapestBooksHandler;
use App\BookStore\Application\Query\FindCheapestBooksQuery;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use PHPUnit\Framework\TestCase;

final class FindCheapestBooksHandlerTest extends TestCase
{
    public function testItReturnsCheapestFirstBoundedBySize(): void
    {
        $books = new InMemoryBookRepository();
        $books->add(new Book(new BookName('Expensive'), new Price(5000)));
        $books->add(new Book(new BookName('Cheap'), new Price(500)));
        $books->add(new Book(new BookName('Medium'), new Price(2000)));

        $handler = new FindCheapestBooksHandler($books);
        $result = iterator_to_array($handler(new FindCheapestBooksQuery(size: 2)));

        self::assertCount(2, $result);
        self::assertSame(500, $result[0]->price->amount);
        self::assertSame(2000, $result[1]->price->amount);
    }
}
