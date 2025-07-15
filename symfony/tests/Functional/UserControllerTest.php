<?php

namespace App\Tests\Functional;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private function createCompany(EntityManagerInterface $em): Company
    {
        $company = new Company();
        $company->setName('Test Company');
        $company->setAddress('123 Rue du Test');
        $company->setCity('Testville');
        $company->setPostcode('00000');

        $em->persist($company);
        $em->flush();

        return $company;
    }

    private function createUser(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, Company $company, array $roles = ['ROLE_ADMIN']): User
    {
        $user = new User();
        $email = 'apiuser_' . uniqid() . '@example.com';

        $user->setEmail($email);
        $user->setPhone('0600000000');
        $user->setRoles($roles);
        $user->setCreatedDate(new \DateTimeImmutable());
        $user->setCompany($company);
        $user->setPassword($hasher->hashPassword($user, 'password123'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testGetAllUsersWithAdminRole(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $company = $this->createCompany($em);
        $user = $this->createUser($em, $hasher, $company, ['ROLE_ADMIN']);

        // Connexion pour obtenir le token
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $user->getEmail(),
            'password' => 'password123'
        ]));

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        $token = $data['token'];

        // Appel GET /api/user avec token
        $client->request('GET', '/api/user', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token
        ]);

        $this->assertResponseIsSuccessful();
        $users = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(1, count($users));
    }

    public function testGetCurrentUserInfo(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $company = $this->createCompany($em);
        $user = $this->createUser($em, $hasher, $company);

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $user->getEmail(),
            'password' => 'password123'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $token = $data['token'];

        $client->request('GET', '/api/user/me', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token
        ]);

        $this->assertResponseIsSuccessful();
        $userData = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals($user->getEmail(), $userData['email']);
        $this->assertEquals($user->getCompany()->getId(), $userData['company']['id']);
        $this->assertArrayHasKey('isCompanySuperAdmin', $userData);
    }
}
