<?php

declare(strict_types=1);

namespace App\Domain\BookRental\Command;

use App\Model\Command;
use Assert\Assertion;

final class RentBookCommand extends Command
{

    private $isbn;

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
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        $isbn = $payload['isbn'];
        Assertion::string($isbn);
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
        return $this->metadata['userId'];
    }
}
