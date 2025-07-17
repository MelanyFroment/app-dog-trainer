<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AuthControllerTest extends WebTestCase
{
    private function createCompany(): Company
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $company = new Company();
        $company->setName('Test Company');
        $company->setAddress('123 Rue du Test');
        $company->setCity('Testville');
        $company->setPostcode('00000');

        $em->persist($company);
        $em->flush();

        return $company;
    }

    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $company = $this->createCompany();
        $email = 'testuser@example.com';

        $client->request('POST', '/api/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $email,
            'password' => 'password123',
            'phone' => '0600000000',
            'company_id' => $company->getId()
        ]));

        $this->assertResponseStatusCodeSame(201);
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Utilisateur créé avec succès', $data['message'] ?? null);
    }

    public function testRegisterMissingData(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => '',
            'password' => '',
            'phone' => ''
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);

        $user = $userRepo->findOneBy(['email' => 'testuser@example.com']);
        $this->assertNotNull($user);

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'testuser@example.com',
            'password' => 'password123'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true));
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]));

        $this->assertResponseStatusCodeSame(401);
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Email ou mot de passe invalide', $data['message'] ?? null);
    }
}
