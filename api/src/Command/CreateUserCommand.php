<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommand extends Command
{

    protected static $defaultName = 'app:create-user';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('firstname', InputArgument::REQUIRED, 'Firstname')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Lastname')
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
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $role = $input->getArgument('role') ?? 'ROLE_USER';


        $command = \App\Domain\User\Command\CreateUserCommand::fromParams(
            $username, $password, $firstname, $lastname, [$role]
        );
        $command->withAddedMetadata('is_admin', true);

        try {
            $this->messageBus->dispatch($command);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $output->writeln('User successfully generated!');
    }
}
