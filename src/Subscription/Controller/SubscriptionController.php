<?php

declare(strict_types=1);

namespace App\Subscription\Controller;

use App\Subscription\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\Serialize;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[AsController]
final class SubscriptionController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/subscriptions', methods: ['POST'])]
    #[Serialize(code: 201)]
    public function create(#[MapRequestPayload] SubscriptionInput $input): Subscription
    {
        $subscription = new Subscription(email: $input->email);

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    #[Route('/subscriptions/{id}', methods: ['GET'])]
    #[Serialize]
    public function get(string $id): Subscription
    {
        $subscription = $this->em->find(Subscription::class, Uuid::fromString($id));

        if (null === $subscription) {
            throw new NotFoundHttpException(\sprintf('No subscription %s', $id));
        }

        return $subscription;
    }
}

final class SubscriptionInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,
    ) {
    }
}
