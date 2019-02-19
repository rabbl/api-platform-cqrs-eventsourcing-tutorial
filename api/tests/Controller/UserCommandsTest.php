<?php

namespace App\Tests\Controller;

use App\Model\BookRental;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class UserCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function createUserCommandTest()
    {
        $admin = $this->createRandomAdmin();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $firstname = sprintf('firstname_%d', rand(1000000, 10000000 - 1));
        $lastname = sprintf('lastname_%d', rand(1000000, 10000000 - 1));
        $roles = ['ROLE_USER'];

        $command = [
            'message_name' => 'createUser',
            'payload' => [
                'username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'roles' => $roles
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($firstname, $user->getFirstname());
        $this->assertEquals($lastname, $user->getLastname());
        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function updateUserCommandTest()
    {
        $admin = $this->createRandomAdmin();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $firstname = sprintf('firstname_%d', rand(1000000, 10000000 - 1));
        $lastname = sprintf('lastname_%d', rand(1000000, 10000000 - 1));
        $roles = ['ROLE_USER'];

        $user = new User($username, $password, $roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();


        $password = $password.'_updated';
        $firstname = $firstname.'_updated';
        $lastname = $lastname.'_updated';
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];

        $command = [
            'message_name' => 'updateUser',
            'payload' => [
                'username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'roles' => $roles
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($firstname, $user->getFirstname());
        $this->assertEquals($lastname, $user->getLastname());
        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function deleteUserCommandTest()
    {
        $admin = $this->createRandomAdmin();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $firstname = sprintf('firstname_%d', rand(1000000, 10000000 - 1));
        $lastname = sprintf('lastname_%d', rand(1000000, 10000000 - 1));
        $roles = ['ROLE_USER'];

        $user = new User($username, $password, $roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();


        $command = [
            'message_name' => 'deleteUser',
            'payload' => [
                'username' => $username
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $this->assertNull($user);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function deleteUserCommandThrowsExceptionIfUserHasRentedBook()
    {
        $admin = $this->createRandomAdmin();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $firstname = sprintf('firstname_%d', rand(1000000, 10000000 - 1));
        $lastname = sprintf('lastname_%d', rand(1000000, 10000000 - 1));
        $roles = ['ROLE_USER'];

        $user = new User($username, $password, $roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);

        $bookRental = new BookRental('a_random_isbn', $user->getId()->toString());
        $em->persist($bookRental);
        $em->flush();

        $command = [
            'message_name' => 'deleteUser',
            'payload' => [
                'username' => $username
            ]
        ];

        $token = $this->getToken($admin->getUsername(), $admin->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
