<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAuthenticationTest extends WebTestCase
{
    public function testAuthenticateAdmin()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(["username" => "admin", "password" => "admin_pw"])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $token = $content['token'];

        $client->request(
            'GET',
            '/api/users',
            [],
            [],
            ['HTTP_Authorization' => sprintf('Bearer %s',  $token)]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAuthenticateUser()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(["username" => "user", "password" => "user_pw"])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $token = $content['token'];

        $client->request(
            'GET',
            '/api/users',
            [],
            [],
            ['HTTP_Authorization' => sprintf('Bearer %s',  $token)]
        );

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
