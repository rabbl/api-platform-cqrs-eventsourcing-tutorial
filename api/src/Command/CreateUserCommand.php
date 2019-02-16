<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommand extends Command
{

    protected static $defaultName = 'app:create-user';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Users role')
            ->setHelp('This command allows you to create a user...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role') ?? 'ROLE_USER';

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user instanceof User) {
            $output->writeln('Username already taken!');
            return;
        }

        try {
            /** @var User $user */
            $user = new User($username, $password, [$role]);
        } catch (\InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $encryptedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encryptedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User successfully generated!');
    }
}
