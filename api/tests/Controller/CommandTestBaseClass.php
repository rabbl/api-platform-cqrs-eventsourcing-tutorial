<?php

namespace App\Tests\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandTestBaseClass extends WebTestCase
{

    /**
     * @param $username
     * @param $password
     * @param array $roles
     * @throws \Exception
     */
    protected function createUser($username, $password, $roles = ['ROLE_USER']): void
    {
        $user = new User($username, $password, $roles);
        /** @var EntityManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush($user);
    }

    /**
     * @param $username
     * @param $password
     * @return string
     */
    protected function getToken($username, $password): string
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(["username" => $username, "password" => $password])
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }

    /**
     * @param $endpoint
     * @param $command
     * @param null $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendCommand($endpoint, $command, $token = null)
    {
        $headers = $token ? ['HTTP_Authorization' => sprintf('Bearer %s',  $token)] : [];
        $headers['CONTENT_TYPE'] = 'application/json';
        $client = static::createClient();
        $client->request(
            'POST',
            $endpoint,
            [],
            [],
            $headers,
            json_encode($command)
        );

        return $client->getResponse();
    }

    /**
     * @param $endpoint
     * @param null $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendRequest($endpoint, $token = null)
    {
        $headers = $token ? ['HTTP_Authorization' => sprintf('Bearer %s',  $token)] : [];
        $headers['CONTENT_TYPE'] = 'application/json';
        $client = static::createClient();
        $client->request(
            'GET',
            $endpoint,
            [],
            [],
            $headers
        );

        return $client->getResponse();
    }


    /**
     * @return User
     * @throws \Exception
     */
    protected function createRandomAdmin(): User
    {
        static::createClient();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));

        $user = new User($username, $password, ['ROLE_ADMIN']);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @return User
     * @throws \Exception
     */
    protected function createRandomUser(): User
    {
        static::createClient();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));

        $user = new User($username, $password, ['ROLE_USER']);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
