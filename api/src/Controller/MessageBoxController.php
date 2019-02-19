<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\BookInventory\Command\AddBooksToInventoryCommand;
use App\Domain\BookInventory\Command\RemoveBooksFromInventoryCommand;
use App\Domain\BookInventory\Command\UpdateBookMetadataCommand;
use App\Domain\BookRental\Command\RentBookCommand;
use App\Domain\BookRental\Command\ReturnBookCommand;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Command\DeleteUserCommand;
use App\Domain\User\Command\UpdateUserCommand;
use App\Model\User;
use App\Model\Command;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class MessageBoxController
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var array */
    private $availableCommands = [
        'addBooksToInventory' => AddBooksToInventoryCommand::class,
        'removeBooksFromInventory' => RemoveBooksFromInventoryCommand::class,
        'updateBookMetadata' => UpdateBookMetadataCommand::class,
        'rentBook' => RentBookCommand::class,
        'returnBook' => ReturnBookCommand::class,
        'createUser' => CreateUserCommand::class,
        'deleteUser' => DeleteUserCommand::class,
        'updateUser' => UpdateUserCommand::class,
    ];

    public function __construct(MessageBusInterface $bus, TokenStorageInterface $tokenStorage)
    {
        $this->commandBus = $bus;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/messagebox", name="messagebox", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            Assertion::eq($request->headers->get('Content-Type'), 'application/json');

            $jsonBody = $request->getContent();
            Assertion::isJsonString($jsonBody, 'Invalid JSON-string, please check your message content.');

            $body = json_decode($jsonBody, true);
            Assertion::keyExists($body, 'message_name', 'Parameter message_name not set.');

            $messageName = $body['message_name'];
            Assertion::string($messageName, 'Message name has to be a string');
            Assertion::keyExists($this->availableCommands, $messageName, sprintf(
                'MessageName: %s not in the list of available commands. Available commands are: %s.',
                $messageName, implode(', ', array_keys($this->availableCommands))
            ));

            Assertion::keyExists($body, 'payload', 'Parameter payload not set.');
            $payload = $body['payload'];

            $commandClass = $this->availableCommands[$messageName];
            $commandClass::assertIsValidPayload($payload);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 422);
        }

        /** @var Command $command */
        $command = $commandClass::fromPayload($payload);

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        # Put some more information in the commands for later use
        $command->withAddedMetadata('userId', $user->getId()->toString());
        $command->withAddedMetadata('is_admin', in_array('ROLE_ADMIN', $user->getRoles()));

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }

        return new JsonResponse([], 202);
    }
}
