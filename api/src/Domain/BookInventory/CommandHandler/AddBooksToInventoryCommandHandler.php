<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\CommandHandler;

use App\Domain\BookInventory\Command\AddBooksToInventoryCommand;
use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;

final class AddBooksToInventoryCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param AddBooksToInventoryCommand $command
     * @throws \Exception
     */
    public function __invoke(AddBooksToInventoryCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }

        $isbn = $command->isbn();
        $name = $command->name();
        $description = $command->description();
        $numberOfPurchasedBooks = $command->numberOfPurchasedBooks();

        $book = $this->entityManager->getRepository(BookInventory::class)->findOneBy(['isbn' => $isbn]);

        if ($book instanceof BookInventory) {
            $book->addBooksToInventory($numberOfPurchasedBooks);
            $this->entityManager->persist($book);
            $this->entityManager->flush();
            return;
        }

        $book = new BookInventory($isbn, $name, $description, $numberOfPurchasedBooks);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
