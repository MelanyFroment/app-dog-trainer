<?php

namespace App\Tests\Unit;

use App\Entity\Client;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ClientEntityTest extends TestCase
{
    public function testClientGettersAndSetters(): void
    {
        $client = new Client();

        $client->setFirstName('Alice');
        $client->setLastName('Martin');
        $client->setPhone('0102030405');

        $this->assertEquals('Alice', $client->getFirstName());
        $this->assertEquals('Martin', $client->getLastName());
        $this->assertEquals('0102030405', $client->getPhone());
    }

    public function testClientEducator(): void
    {
        $client = new Client();
        $user = new User();

        $client->setEducator($user);
        $this->assertSame($user, $client->getEducator());
    }
}
