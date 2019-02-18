<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Model\Command;
use Assert\Assertion;

final class DeleteUserCommand extends Command
{
    private $username;

    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload)
    {
        static::assertIsValidPayload($payload);

        $self = new self();
        $self->username = $payload['username'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        Assertion::string($payload['username']);
    }

    private function __construct()
    {
    }

    public function username(): string
    {
        return $this->username;
    }
}
