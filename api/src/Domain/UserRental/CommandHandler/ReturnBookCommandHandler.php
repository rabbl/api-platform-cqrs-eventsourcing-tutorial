<?php

declare(strict_types=1);

namespace App\Domain\UserRental\CommandHandler;

use App\Domain\UserRental\Command\RentBookCommand;
use App\Model\BookInventory;
use App\Model\UserRental;
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

        $userRental = $this->entityManager
            ->getRepository(UserRental::class)
            ->findOneBy(['userId' => $userId,'isbn' => $isbn, 'returned' => null]);

        if (!$userRental instanceof UserRental) {
            throw new \Exception(sprintf('Rented Book by User %s with ISBN %s not found.', $userId, $isbn));
        }

        $book->returnBook();
        $this->entityManager->persist($book);

        $userRental->bookHasBeenReturned();
        $this->entityManager->persist($userRental);
        $this->entityManager->flush();
    }
}
