<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\Command;
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
    private $availableCommands = [];

    public function __construct(MessageBusInterface $bus, TokenStorageInterface $tokenStorage)
    {
        $this->commandBus = $bus;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/messagebox", name="messagebox", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            [$messageName, $payload] = $this->getMessageNameAndPayload($request);
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

        $this->commandBus->dispatch($command);
        return new JsonResponse([], 202);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function getMessageNameAndPayload(Request $request): array
    {

        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            throw new \RuntimeException('Expecting Header: Content-Type: application/json');
        }

        $body = \json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON received.');
        }

        $message_name = $body['message_name'] ?? null;

        if (!$message_name) {
            throw new \Exception(sprintf('Parameter message_name not given or null.'));
        }

        if (!array_key_exists($message_name, $this->availableCommands)) {
            throw new \Exception(
                sprintf(
                    'MessageName: %s not in the list of available commands. Available commands are: %s.',
                    $message_name, implode(', ', array_keys($this->availableCommands))
                )
            );
        }

        $payload = $body['payload'] ?? null;

        if (null === $payload) {
            throw new \Exception('Parameter payload expected.');
        }

        return [$message_name, $payload];
    }
}
