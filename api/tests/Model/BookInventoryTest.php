<?php

namespace App\Tests\Controller;

use App\Model\BookInventory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookInventoryTest extends WebTestCase
{

    public function provider()
    {
        return [[
            '978-'.rand(100000, 1000000),
            'The Ultimate Hitchhiker\'s Guide to the Galaxy: Five Novels in One Outrageous Volume',
            'At last in paperback in one complete volume, here are the five classic novels from Douglas Adams\'s beloved Hitchhiker series.'
        ]];
    }

    /**
     * @dataProvider provider
     * @param $isbn
     * @param $name
     * @param $description
     * @throws \Exception
     */
    public function testBookInventoryService($isbn, $name, $description): void
    {
        $client = static::createClient();
        $book = new BookInventory($isbn, $name, $description, 5);

        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($book);
        $em->flush();
        $em->clear();

        /** @var BookInventory $book */
        $book = $em->getRepository(BookInventory::class)->findOneBy(['isbn' => $isbn]);
        $this->assertInstanceOf(BookInventory::class, $book);
        $this->assertEquals($isbn, $book->getIsbn());
        $this->assertEquals($name, $book->getName());
        $this->assertEquals($description, $book->getDescription());
        $this->assertEquals(5, $book->getTotalNumberInInventory());
        $this->assertEquals(5, $book->getTotalNumberInLibrary());
        $this->assertEquals(0, $book->getTotalNumberRented());

        $book->rentBook();
        $this->assertEquals(5, $book->getTotalNumberInInventory());
        $this->assertEquals(4, $book->getTotalNumberInLibrary());
        $this->assertEquals(1, $book->getTotalNumberRented());

        $book->removeBooksFromInventory(2);
        $this->assertEquals(3, $book->getTotalNumberInInventory());
        $this->assertEquals(2, $book->getTotalNumberInLibrary());
        $this->assertEquals(1, $book->getTotalNumberRented());

        $book->returnBook();
        $this->assertEquals(3, $book->getTotalNumberInInventory());
        $this->assertEquals(3, $book->getTotalNumberInLibrary());
        $this->assertEquals(0, $book->getTotalNumberRented());


    }
}
