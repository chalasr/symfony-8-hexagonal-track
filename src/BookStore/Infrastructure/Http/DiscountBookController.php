<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Http;

use App\BookStore\Application\Command\DiscountBookCommand;
use App\BookStore\Application\Dto\DiscountBookPayload;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\Discount;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\Serialize;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/** Slide 20 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=20 */
#[AsController]
final class DiscountBookController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/books/{id}/discount', methods: ['POST'], requirements: ['id' => Requirement::UUID])]
    #[Serialize(code: 204)]
    public function __invoke(
        string $id,
        #[MapRequestPayload] DiscountBookPayload $payload,
    ): void {
        $this->commandBus->dispatch(new DiscountBookCommand(
            new BookId($id),
            new Discount($payload->percentage),
        ));
    }
}
