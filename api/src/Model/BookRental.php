<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="book_rentals")
 */
class BookRental
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="user_id", type="string", unique=true, nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", nullable=false)
     */
    private $isbn;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="issued", type="datetime_immutable", nullable=false)
     */
    private $issued;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="returned", type="datetime_immutable")
     */
    private $returned;

    /**
     * User constructor.
     * @param string $userId
     * @param string $isbn
     */
    public function __construct(string $userId, string $isbn)
    {
        $this->userId = $userId;
        $this->isbn = $isbn;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getIssued(): \DateTimeImmutable
    {
        return $this->issued;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getReturned(): ?\DateTimeImmutable
    {
        return $this->returned;
    }

    /**
     * @throws \Exception
     */
    public function bookHasBeenReturned(): void
    {
        $this->returned = new \DateTimeImmutable('now');
    }
}
