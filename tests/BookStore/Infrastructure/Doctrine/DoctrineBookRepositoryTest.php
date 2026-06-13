<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The one BookStore test that talks to a real DB. Rebinds the port to the Doctrine
 * adapter for itself only — every other test keeps the in-memory adapter from
 * services_test.yaml.
 */
final class DoctrineBookRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private DoctrineBookRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(DoctrineBookRepository::class);

        // Per-test override: rebind the port to the Doctrine adapter for this test only.
        $container->set(BookRepositoryInterface::class, $this->repository);

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testItPersistsAndRetrievesBook(): void
    {
        $book = new Book(new BookName('Patterns of Enterprise App Arch'), new Price(4500));
        $this->repository->add($book);

        $this->em->clear();

        $retrieved = $this->repository->ofId($book->id);

        self::assertNotNull($retrieved);
        self::assertSame('Patterns of Enterprise App Arch', $retrieved->name->value);
        self::assertSame(4500, $retrieved->price->amount);
    }
}
