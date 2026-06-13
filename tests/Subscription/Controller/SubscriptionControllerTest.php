<?php

declare(strict_types=1);

namespace App\Tests\Subscription\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** RAD path — no in-memory port, so this test hits SQLite directly. */
final class SubscriptionControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testItCreatesSubscriptionFromJson(): void
    {
        $this->client->request(
            'POST',
            '/subscriptions',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['email' => 'you@example.com'], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(201);

        $payload = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('you@example.com', $payload['email']);
        self::assertNotEmpty($payload['id']);
    }

    public function testItRejectsInvalidEmail(): void
    {
        $this->client->request(
            'POST',
            '/subscriptions',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['email' => 'not-an-email'], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(422);
    }

    public function testItRejectsMissingEmail(): void
    {
        $this->client->request(
            'POST',
            '/subscriptions',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{}',
        );

        self::assertResponseStatusCodeSame(422);
    }
}
