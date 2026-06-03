<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * `implements CommandHandlerInterface` is kept for clarity: it's still a valid
 * Application-layer port even though `#[AsMessageHandler]` does the binding.
 */
#[AsMessageHandler(bus: 'command.bus')]
final class DiscountBookHandler implements CommandHandlerInterface
{
    public function __construct(
        private BookRepositoryInterface $books,
    ) {
    }

    public function __invoke(DiscountBookCommand $command): void
    {
        $book = $this->books->ofId($command->id);
        if (null === $book) {
            throw new MissingBookException($command->id);
        }

        $book->applyDiscount($command->discount);

        $this->books->remove($book);
        $this->books->add($book);
    }
}
