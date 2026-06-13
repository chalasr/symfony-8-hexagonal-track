<?php

declare(strict_types=1);

namespace App\Subscription\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slide 28 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=28
 *
 * RAD. ORM + Assert on the entity; never `#[Route]` here — that belongs on the
 * controller.
 */
#[ORM\Entity]
class Subscription
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        public ?Uuid $id = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        #[ORM\Column(name: 'email', nullable: false)]
        public ?string $email = null,
    ) {
        $this->id ??= Uuid::v4();
    }
}
