<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Infrastructure\InMemory\InMemoryRepository;

/**
 * @extends InMemoryRepository<Book>
 */
final class InMemoryBookRepository extends InMemoryRepository implements BookRepositoryInterface
{
    public function add(Book $book): void
    {
        $this->entities[(string) $book->id] = $book;
    }

    public function remove(Book $book): void
    {
        unset($this->entities[(string) $book->id]);
    }

    public function ofId(BookId $id): ?Book
    {
        return $this->entities[(string) $id] ?? null;
    }

    public function withCheapestsFirst(): static
    {
        $cloned = clone $this;

        uasort($cloned->entities, static fn (Book $a, Book $b) => $a->price->amount <=> $b->price->amount);

        return $cloned;
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        $cloned = clone $this;

        $cloned->entities = \array_slice($cloned->entities, ($page - 1) * $itemsPerPage, $itemsPerPage, true);

        return $cloned;
    }

    public function getIterator(): \Iterator
    {
        yield from array_values($this->entities);
    }
}
