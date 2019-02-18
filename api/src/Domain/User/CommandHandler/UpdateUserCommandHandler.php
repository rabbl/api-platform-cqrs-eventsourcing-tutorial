<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Command\UpdateUserCommand;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UpdateUserCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param UpdateUserCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateUserCommand $command)
    {
        # only admins can do that
        if (!$command->metadata()['is_admin']) {
            throw new \Exception('Only admins can do that');
        }

        $username = $command->username();
        $password = $command->password();
        $firstname = $command->firstname();
        $lastname = $command->lastname();
        $roles = $command->roles();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with username %s not found.', $username));
        }

        $user->setUsername($username);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRoles($roles);

        $encryptedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encryptedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
