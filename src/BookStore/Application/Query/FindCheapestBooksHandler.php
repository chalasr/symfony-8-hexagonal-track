<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class FindCheapestBooksHandler implements QueryHandlerInterface
{
    public function __construct(
        private BookRepositoryInterface $books,
    ) {
    }

    public function __invoke(FindCheapestBooksQuery $query): iterable
    {
        return $this->books
            ->withCheapestsFirst()
            ->withPagination(1, $query->size);
    }
}
