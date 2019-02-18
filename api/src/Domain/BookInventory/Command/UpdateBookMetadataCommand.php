<?php

declare(strict_types=1);

namespace App\Domain\BookInventory\Command;

use App\Model\Command;
use Assert\Assertion;

final class UpdateBookMetadataCommand extends Command
{

    private $isbn;
    private $name;
    private $description;

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
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        return $self;
    }

    public static function assertIsValidPayload(array $payload)
    {
        $isbn = $payload['isbn'];
        Assertion::string($isbn);

        $name = $payload['name'];
        Assertion::string($name);

        $description = $payload['description'];
        Assertion::string($description);
    }

    private function __construct()
    {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }
}
