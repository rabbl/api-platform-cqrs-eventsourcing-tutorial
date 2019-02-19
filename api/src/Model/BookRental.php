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
     * @ORM\Column(name="uuid", type="uuid", unique=true, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="user_id", type="string", nullable=false)
     */
    private $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", nullable=false)
     */
    private $userId;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="issued", type="datetime_immutable", nullable=false)
     */
    private $issued;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="returned", type="datetime_immutable", nullable=true)
     */
    private $returned;

    /**
     * User constructor.
     * @param string $isbn
     * @param string $userId
     * @throws \Exception
     */
    public function __construct(string $isbn, string $userId)
    {
        $this->uuid = Uuid::uuid4();
        $this->isbn = $isbn;
        $this->userId = $userId;
        $this->issued = new \DateTimeImmutable('now');
    }

    /**
     * @return Uuid
     */
    public function getUserId(): string
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
    public function returnBook(): void
    {
        $this->returned = new \DateTimeImmutable('now');
    }
}
