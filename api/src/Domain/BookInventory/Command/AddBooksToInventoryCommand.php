<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\Command;

use App\Model\Command;
use Assert\Assertion;

final class AddBooksToInventoryCommand extends Command
{

    private $isbn;
    private $name;
    private $description;
    private $numberOfPurchasedBooks;


    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload)
    {
        static::assertIsValidPayload($payload);

        $self = new self();
        $self->isbn = $payload['isbn'];
        $self->name = $payload['name'] ?? null;
        $self->description = $payload['description'] ?? null;
        $self->numberOfPurchasedBooks = $payload['numberOfPurchasedBooks'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        $isbn = $payload['isbn'];
        Assertion::string($isbn);

        $numberOfPurchasedBooks = $payload['numberOfPurchasedBooks'];
        Assertion::greaterThan($numberOfPurchasedBooks, 0);
    }

    private function __construct()
    {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function numberOfPurchasedBooks(): int
    {
        return $this->numberOfPurchasedBooks;
    }
}
