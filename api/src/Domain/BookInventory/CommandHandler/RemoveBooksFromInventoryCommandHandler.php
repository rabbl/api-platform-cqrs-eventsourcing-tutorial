<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\CommandHandler;

use App\Domain\BookInventory\Command\RemoveBooksFromInventoryCommand;
use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;

final class RemoveBooksFromInventoryCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param RemoveBooksFromInventoryCommand $command
     * @throws \Exception
     */
    public function __invoke(RemoveBooksFromInventoryCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }

        $isbn = $command->isbn();
        $numberOfBooks = $command->numberOfBooks();

        $book = $this->entityManager->getRepository(BookInventory::class)->findOneBy(['isbn' => $isbn]);

        if (!$book instanceof BookInventory) {
            throw new \Exception(sprintf('Book with ISBN %s not found.', $isbn));
        }

        $book->removeBooksFromInventory($numberOfBooks);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
