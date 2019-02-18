<?php

declare(strict_types=1);

namespace App\Domain\BookRental\CommandHandler;

use App\Domain\BookRental\Command\RentBookCommand;
use App\Model\BookInventory;
use App\Model\BookRental;
use Doctrine\ORM\EntityManagerInterface;

final class ReturnBookCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param RentBookCommand $command
     * @throws \Exception
     */
    public function __invoke(RentBookCommand $command)
    {

        $isbn = $command->isbn();
        $userId = $command->userId();

        $book = $this->entityManager->getRepository(BookInventory::class)->findOneBy(['isbn' => $isbn]);

        if (!$book instanceof BookInventory) {
            throw new \Exception(sprintf('Book with ISBN %s not found.', $isbn));
        }

        $bookRental = $this->entityManager
            ->getRepository(BookRental::class)
            ->findOneBy(['isbn' => $isbn, 'userId' => $userId, 'returned' => null]);

        if (!$bookRental instanceof BookRental) {
            throw new \Exception(sprintf('Rented book by user %s with ISBN %s not found.', $userId, $isbn));
        }

        $book->returnBook();
        $this->entityManager->persist($book);

        $bookRental->bookHasBeenReturned();
        $this->entityManager->persist($bookRental);
        $this->entityManager->flush();
    }
}
