<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Infrastructure\Http;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CheapestBooksControllerTest extends WebTestCase
{
    public function testItReturnsCheapestBooks(): void
    {
        $client = self::createClient();

        /** @var InMemoryBookRepository $books */
        $books = self::getContainer()->get(BookRepositoryInterface::class);
        $books->add(new Book(new BookName('Expensive'), new Price(5000)));
        $books->add(new Book(new BookName('Cheap'), new Price(500)));
        $books->add(new Book(new BookName('Medium'), new Price(2000)));

        $client->request('GET', '/books/cheapest?size=2');

        self::assertResponseIsSuccessful();
        $payload = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $payload);
        self::assertSame(500, $payload[0]['price']);
        self::assertSame(2000, $payload[1]['price']);
    }
}
