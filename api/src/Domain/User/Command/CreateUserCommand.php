<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Model\Command;
use Assert\Assertion;

final class CreateUserCommand extends Command
{
    private $username;
    private $password;
    private $firstname;
    private $lastname;
    private $roles;

    /**
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param $role
     * @return CreateUserCommand
     */
    public static function fromCLICommand(string $username, string $password, string $firstname, string $lastname, $role): self
    {
        $self = new self();
        $self->username = $username;
        $self->password = $password;
        $self->firstname = $firstname;
        $self->lastname = $lastname;
        $self->roles = [$role];
        $self->metadata['is_admin'] = true;
        return $self;
    }

    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload): self
    {
        static::assertIsValidPayload($payload);

        $self = new self();
        $self->username = $payload['username'];
        $self->password = $payload['password'];
        $self->firstname = $payload['firstname'];
        $self->lastname = $payload['lastname'];
        $self->roles = $payload['roles'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        Assertion::string($payload['username']);
        Assertion::string($payload['password']);
        Assertion::string($payload['firstname']);
        Assertion::string($payload['lastname']);
        Assertion::isArray($payload['roles']);
    }

    private function __construct()
    {
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function firstname(): string
    {
        return $this->firstname;
    }

    public function lastname(): string
    {
        return $this->lastname;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
