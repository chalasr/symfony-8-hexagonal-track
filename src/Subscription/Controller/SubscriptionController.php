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
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

/** Slide 28 — https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=28 */
#[AsController]
final class SubscriptionController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/subscriptions', methods: ['POST'])]
    #[Serialize(code: 201)]
    public function create(#[MapRequestPayload] Subscription $subscription): Subscription
    {
        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    #[Route('/subscriptions/{id}', methods: ['GET'], requirements: ['id' => Requirement::UUID])]
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
