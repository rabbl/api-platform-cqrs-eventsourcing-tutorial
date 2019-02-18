<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\CommandHandler;

use App\Domain\BookInventory\Command\UpdateBookMetadataCommand;
use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateBookMetadataCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UpdateBookMetadataCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateBookMetadataCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }

        $isbn = $command->isbn();
        $name = $command->name();
        $description = $command->description();

        /** @var BookInventory $book */
        $book = $this->entityManager->getRepository(BookInventory::class)->findOneBy(['isbn' => $isbn]);

        if (!$book instanceof BookInventory) {
            throw new \Exception(sprintf('Book with ISBN %s not found in inventory.', $isbn));
        }

        $book->setName($name);
        $book->setDescription($description);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
