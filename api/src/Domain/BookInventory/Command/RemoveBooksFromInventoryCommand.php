<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\Command;

use App\Model\Command;
use Assert\Assertion;

final class RemoveBooksFromInventoryCommand extends Command
{

    private $isbn;
    private $numberOfBooks;

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
        $self->numberOfBooks = $payload['numberOfBooks'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        $isbn = $payload['isbn'];
        Assertion::string($isbn);

        $numberOfBooks = $payload['numberOfBooks'];
        Assertion::greaterThan($numberOfBooks, 0);
    }

    private function __construct()
    {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function numberOfBooks(): int
    {
        return $this->numberOfBooks;
    }
}
