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
use Symfony\Component\Uid\Uuid;

/**
 * Slide 20: the HTTP adapter. Invokable, no `extends AbstractController`.
 * `#[MapRequestPayload]` does deserialize + validate in one (Sf 6.3).
 * `#[Serialize]` (Sf 8.1) replaces a manual JsonResponse wrap.
 */
#[AsController]
final class DiscountBookController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/books/{id}/discount', methods: ['POST'])]
    #[Serialize(code: 204)]
    public function __invoke(
        string $id,
        #[MapRequestPayload] DiscountBookPayload $payload,
    ): void {
        $this->commandBus->dispatch(new DiscountBookCommand(
            new BookId(Uuid::fromString($id)),
            new Discount($payload->percentage),
        ));
    }
}
