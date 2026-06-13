<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidIdentifierException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

abstract class AggregateRootId implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid')]
    public readonly AbstractUid $value;

    public function __construct(AbstractUid|string|null $value = null)
    {
        if ($value instanceof AbstractUid) {
            $this->value = $value;

            return;
        }

        if (null === $value) {
            $this->value = Uuid::v4();

            return;
        }

        if (!Uuid::isValid($value)) {
            throw InvalidIdentifierException::notAUuid($value);
        }

        $this->value = Uuid::fromString($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
