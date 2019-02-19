<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Command\DeleteUserCommand;
use App\Model\BookRental;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteUserCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DeleteUserCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteUserCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }

        $username = $command->username();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with username %s not found.', $username));
        }

        $rentedBooks = $this->entityManager
            ->getRepository(BookRental::class)
            ->findBy(['userId' => $user->getId()->toString(), 'returned' => null]);

        if (count($rentedBooks) > 0) {
            throw new \Exception(sprintf('User with Id: %s cannot be deleted, due to open book rentals.', $user->getId()));
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
