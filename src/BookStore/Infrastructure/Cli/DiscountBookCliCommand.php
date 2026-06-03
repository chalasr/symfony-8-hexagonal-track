<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Cli;

use App\BookStore\Application\Command\DiscountBookCommand;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\Discount;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('books:discount', 'Apply a discount to a book')]
final class DiscountBookCliCommand
{
    public function __construct(private CommandBusInterface $bus)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Book UUID')] string $id,
        #[Argument(description: 'Discount percentage (1-100)')] int $percentage,
    ): int {
        $this->bus->dispatch(new DiscountBookCommand(
            new BookId($id),
            new Discount($percentage),
        ));

        $io->success(sprintf('Discounted book %s by %d%%', $id, $percentage));

        return Command::SUCCESS;
    }
}
