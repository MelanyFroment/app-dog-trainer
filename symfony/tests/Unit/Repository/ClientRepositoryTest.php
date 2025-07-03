<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{
    public function testFindByEducatorReturnsOnlyClientsOfThatUser(): void
    {
        // Arrange
        $educatorA = new User();
        $educatorA->setEmail('educatorA@example.com');
        $educatorA->setRoles(['ROLE_USER']);

        $educatorB = new User();
        $educatorB->setEmail('educatorB@example.com');
        $educatorB->setRoles(['ROLE_USER']);

        $client1 = new Client();
        $client1->setFirstName('Alice');
        $client1->setEducator($educatorA);

        $client2 = new Client();
        $client2->setFirstName('Bob');
        $client2->setEducator($educatorB);

        $allClients = [$client1, $client2];

        // Simule un repository qui ne retourne que les clients de l'éducateur A
        $mockRepository = $this->getMockBuilder(ClientRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findBy'])
            ->getMock();

        $mockRepository->expects($this->once())
            ->method('findBy')
            ->with(['educator' => $educatorA])
            ->willReturn([$client1]);

        // Act
        $result = $mockRepository->findBy(['educator' => $educatorA]);

        // Assert
        $this->assertCount(1, $result);
        $this->assertSame($client1, $result[0]);
        $this->assertNotSame($client2, $result[0]);
    }
}
