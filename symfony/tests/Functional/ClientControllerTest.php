<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{
    public function testAccessDeniedForUnauthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/clients');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
