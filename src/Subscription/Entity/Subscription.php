<?php

declare(strict_types=1);

namespace App\Subscription\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slide 28: RAD coexistence. Plain entity, ORM + Assert + (controller-level) Route — all on one class.
 * This is the right tier for a settled-shape, three-field, single-CRUD-form domain.
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
