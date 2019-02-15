<?php

declare(strict_types=1);

namespace App\Domain\UserRental\Command;

use App\Model\Command;
use Assert\Assertion;

final class ReturnBookCommand extends Command
{

    private $isbn;
    private $userId;

    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload): self
    {
        static::assertIsValidPayload($payload);

        $self = new self();
        $self->isbn = $payload['isbn'];
        $self->userId = $payload['userId'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        $isbn = $payload['isbn'];
        Assertion::string($isbn);

        $userId = $payload['userId'];
        Assertion::string($userId);
    }

    private function __construct()
    {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
