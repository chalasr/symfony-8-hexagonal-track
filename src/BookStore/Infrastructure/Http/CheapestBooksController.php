<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Http;

use App\BookStore\Application\Dto\BookResource;
use App\BookStore\Application\Dto\CheapestBooksFilter;
use App\BookStore\Application\Query\FindCheapestBooksQuery;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\Serialize;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Slide 23: GET adapter with `#[MapQueryString]` + `#[Serialize]`.
 * Uses `ObjectMapperInterface` (slide 25) to project domain `Book`s to `BookResource` DTOs.
 */
#[AsController]
final class CheapestBooksController
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ObjectMapperInterface $mapper,
    ) {
    }

    /**
     * @return list<BookResource>
     */
    #[Route('/books/cheapest', methods: ['GET'])]
    #[Serialize(context: ['groups' => ['book:read']])]
    public function __invoke(#[MapQueryString] ?CheapestBooksFilter $filter = null): array
    {
        $filter ??= new CheapestBooksFilter();

        $books = $this->queryBus->ask(new FindCheapestBooksQuery($filter->size));

        $resources = [];
        foreach ($books as $book) {
            $resources[] = $this->mapper->map($book, BookResource::class);
        }

        return $resources;
    }
}
