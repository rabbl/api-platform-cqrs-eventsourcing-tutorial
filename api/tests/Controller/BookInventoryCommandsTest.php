<?php

namespace App\Tests\Controller;

use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;

class BookInventoryCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function addBooksToInventoryTest(): void
    {
        $admin = $this->createRandomAdmin();
        $book = new BookInventory(
            '978-0345453747',
            'The Ultimate Hitchhiker\'s Guide to the Galaxy: Five Novels in One Outrageous Volume',
            'At last in paperback in one complete volume, here are the five classic novels from Douglas Adams\'s beloved Hitchhiker series.',
            5
        );

        $command = [
            'message_name' => 'addBooksToInventory',
            'payload' => [
                'isbn' => $book->getIsbn(),
                'name' => $book->getName(),
                'description' => $book->getDescription(),
                'numberOfPurchasedBooks' => $book->getTotalNumberInInventory()
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var BookInventory $addedBook */
        $addedBook = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $book->getIsbn()]);
        $this->assertInstanceOf(BookInventory::class, $addedBook);
        $this->assertEquals($addedBook, $book);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function updateBookMetadataCommand(): void
    {
        $admin = $this->createRandomAdmin();
        $book = $this->addRandomBookToInventory();
        $book->setName($book->getName().'_'.rand(1000000, 10000000));
        $book->setDescription($book->getDescription().'_'.rand(1000000, 10000000));

        $command = [
            'message_name' => 'updateBookMetadata',
            'payload' => [
                'isbn' => $book->getIsbn(),
                'name' => $book->getName(),
                'description' => $book->getDescription()
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var BookInventory $updatedBook */
        $updatedBook = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $book->getIsbn()]);
        $this->assertInstanceOf(BookInventory::class, $updatedBook);
        $this->assertEquals($updatedBook, $book);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function removeBooksFromInventoryCommand(): void
    {
        $admin = $this->createRandomAdmin();
        $book = $this->addRandomBookToInventory();

        $command = [
            'message_name' => 'removeBooksFromInventory',
            'payload' => [
                'isbn' => $book->getIsbn(),
                'numberOfBooks' => 2
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var BookInventory $updatedBook */
        $updatedBook = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $book->getIsbn()]);
        $this->assertInstanceOf(BookInventory::class, $updatedBook);
        $this->assertEquals($book->getTotalNumberInInventory()-2, $updatedBook->getTotalNumberInInventory());
    }
}
