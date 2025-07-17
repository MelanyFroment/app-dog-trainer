<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): array
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get('doctrine.orm.entity_manager');
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $company = new Company();
        $company->setName('Test Company');
        $company->setAddress('123 Rue du Test');
        $company->setCity('Testville');
        $company->setPostcode('00000');
        $em->persist($company);

        $user = new User();
        $user->setEmail('educator_' . uniqid() . '@test.com');
        $user->setPassword($hasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        $user->setPhone('0600000000');
        $user->setCreatedDate(new \DateTimeImmutable());
        $user->setCompany($company);
        $user->setIsCompanySuperAdmin(false);
        $em->persist($user);
        $em->flush();

        // Authentification
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $user->getEmail(),
            'password' => 'password123'
        ]));

        $response = json_decode($client->getResponse()->getContent(), true);
        $token = $response['token'] ?? null;

        return [$client, $token];
    }

    public function testCreateClient(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request('POST', '/api/clients', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'firstname' => 'Jean',
            'lastname' => 'Dupont',
            'address' => '10 rue des Lilas',
            'postalCode' => '75000',
            'city' => 'Paris',
            'phone' => '0123456789',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Client créé avec succès', $response['message'] ?? null);
    }
}
