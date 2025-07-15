<?php

namespace App\Tests\Unit;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class CompanyEntityTest extends TestCase
{
    public function testCompanyEntitySettersAndGetters(): void
    {
        $company = new Company();

        $company->setName('DogCorp');
        $company->setAddress('12 Avenue K9');
        $company->setCity('DogCity');
        $company->setPostcode('12345');
        $company->setEmail('dogcorp@example.com');
        $company->setPhone('0612345678');
        $company->setInformation('Centre d’éducation canine');

        $this->assertEquals('DogCorp', $company->getName());
        $this->assertEquals('12 Avenue K9', $company->getAddress());
        $this->assertEquals('DogCity', $company->getCity());
        $this->assertEquals('12345', $company->getPostcode());
        $this->assertEquals('dogcorp@example.com', $company->getEmail());
        $this->assertEquals('0612345678', $company->getPhone());
        $this->assertEquals('Centre d’éducation canine', $company->getInformation());
    }

    public function testAddAndRemoveUser(): void
    {
        $company = new Company();
        $user = new User();

        $this->assertCount(0, $company->getUsers());

        $company->addUser($user);
        $this->assertCount(1, $company->getUsers());
        $this->assertSame($company, $user->getCompany());

        $company->removeUser($user);
        $this->assertCount(0, $company->getUsers());
        $this->assertNull($user->getCompany());
    }

    public function testAddAndRemoveClient(): void
    {
        $company = new Company();
        $client = new Client();

        $this->assertCount(0, $company->getClients());

        $company->addClient($client);
        $this->assertCount(1, $company->getClients());
        $this->assertSame($company, $client->getCompany());

        $company->removeClient($client);
        $this->assertCount(0, $company->getClients());
        $this->assertNull($client->getCompany());
    }
}
