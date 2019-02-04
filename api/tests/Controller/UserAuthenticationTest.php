<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAuthenticationTest extends WebTestCase
{

    public function provider()
    {
        return [
            ['admin', 'admin_pw', 200],
            ['user', 'user_pw', 403]
        ];
    }

    /**
     * @dataProvider provider
     * @param $username
     * @param $password
     * @param $statusCode
     */
    public function testAuthentication($username, $password, $statusCode)
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
        $token = $content['token'];

        $client->request(
            'GET',
            '/api/users',
            [],
            [],
            ['HTTP_Authorization' => sprintf('Bearer %s',  $token)]
        );

        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
    }
}
