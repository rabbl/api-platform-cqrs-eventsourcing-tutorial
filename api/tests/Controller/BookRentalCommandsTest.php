<?php

namespace App\Tests\Controller;

use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;

class BookRentalCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function rentAndReturnBookCommandTest(): void
    {
        $user = $this->createRandomUser();
        $book = $this->addRandomBookToInventory();

        $command = [
            'message_name' => 'rentBook',
            'payload' => [
                'isbn' => $book->getIsbn()
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var BookInventory $updated */
        $updated = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $book->getIsbn()]);
        $this->assertInstanceOf(BookInventory::class, $updated);
        $this->assertEquals($updated->getTotalNumberInInventory(), $book->getTotalNumberInInventory());
        $this->assertEquals($updated->getTotalNumberRented(), $book->getTotalNumberRented()+1);
        $this->assertEquals($updated->getTotalNumberInLibrary(), $book->getTotalNumberInLibrary()-1);

        $command = [
            'message_name' => 'returnBook',
            'payload' => [
                'isbn' => $book->getIsbn()
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var BookInventory $updated */
        $updated = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $book->getIsbn()]);
        $this->assertInstanceOf(BookInventory::class, $updated);
        $this->assertEquals($updated, $book);
    }
}
