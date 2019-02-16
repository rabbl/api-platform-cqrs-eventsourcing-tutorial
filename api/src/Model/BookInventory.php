<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="book_inventory")
 */
class BookInventory
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="isbn", type="string", unique=true, nullable=false)
     */
    private $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="total_number_in_inventory", type="integer", nullable=false)
     */
    private $totalNumberInInventory;

    /**
     * @var int
     *
     * @ORM\Column(name="total_number_rented", type="integer", nullable=false)
     */
    private $totalNumberRented;

    /**
     * User constructor.
     * @param string $isbn
     * @param string $name
     * @param string $description
     * @param int $numberOfPurchasedBooks
     */
    public function __construct(string $isbn, string $name, string $description, int $numberOfPurchasedBooks)
    {
        $this->isbn = $isbn;
        $this->name = $name;
        $this->description = $description;
        $this->totalNumberInInventory = $numberOfPurchasedBooks;
    }

    /**
     * @return Uuid
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getTotalNumberInInventory(): int
    {
        return $this->totalNumberInInventory;
    }

    /**
     * @param int $totalNumberInInventory
     */
    public function setTotalNumberInInventory(int $totalNumberInInventory): void
    {
        $this->totalNumberInInventory = $totalNumberInInventory;
    }

    /**
     * @return int
     */
    public function getTotalNumberRented(): int
    {
        return $this->totalNumberRented;
    }

    /**
     * @param int $totalNumberRented
     */
    public function setTotalNumberRented(int $totalNumberRented): void
    {
        $this->totalNumberRented = $totalNumberRented;
    }

    public function addBooksToInventory(int $numberOfBooks): void
    {
        $this->totalNumberInInventory += $numberOfBooks;
    }

    /**
     * @param int $numberOfBooks
     * @throws \Exception
     */
    public function removeBooksFromInventory(int $numberOfBooks): void
    {
        if ($this->totalNumberInInventory - $this->totalNumberRented < $numberOfBooks) {
            throw new \Exception('Not enough books in inventory to remove');
        }

        $this->totalNumberInInventory -= $numberOfBooks;
    }

    /**
     * @throws \Exception
     */
    public function rentBook(): void
    {
        if ($this->totalNumberInInventory - $this->totalNumberRented <= 1) {
            throw new \Exception('There has to stay at least one book in inventory.');
        }

        $this->totalNumberRented += 1;
    }

    public function returnBook(): void
    {
        $this->totalNumberRented -= 1;
    }
}
