<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Infrastructure\Http;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * WebTestCase + in-memory repo (via the test alias in services_test.yaml).
 * No database needed.
 */
final class DiscountBookControllerTest extends WebTestCase
{
    public function testItDiscountsBookAndReturns204(): void
    {
        $client = self::createClient();

        /** @var InMemoryBookRepository $books */
        $books = self::getContainer()->get(BookRepositoryInterface::class);
        $book = new Book(new BookName('Refactoring'), new Price(10000));
        $books->add($book);

        $client->request(
            'POST',
            '/books/'.$book->id.'/discount',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['percentage' => 25], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(204);
        self::assertSame(7500, $books->ofId($book->id)->price->amount);
    }
}
