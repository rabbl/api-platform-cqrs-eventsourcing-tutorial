<?php

declare(strict_types=1);

namespace App\Domain\UserRental\CommandHandler;

use App\Domain\UserRental\Command\RentBookCommand;
use App\Model\BookInventory;
use App\Model\BookRental;
use Doctrine\ORM\EntityManagerInterface;

final class RentBookCommandHandler
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


        $rentedBooks = $this->entityManager->getRepository(BookRental::class)->findBy(['userId' => $userId, 'returned' => null]);
        if (count($rentedBooks) >= 3) {
            throw new \Exception(sprintf('Renting more then 3 books is not allowed.'));
        }

        $book->rentBook();
        $this->entityManager->persist($book);

        $userRental = new BookRental($userId, $isbn);
        $this->entityManager->persist($userRental);
        $this->entityManager->flush();
    }
}

