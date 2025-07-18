<?php

namespace App\Tests\Functional;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(bool $isAdmin = false): array
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('apiuser' . uniqid() . '@example.com');
        $user->setPhone('0123456789');
        $user->setCreatedDate(new \DateTimeImmutable());
        $user->setPassword($passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        // Authentifie l'utilisateur via le login
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $user->getEmail(),
            'password' => 'password123'
        ]));

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        return [$client, $token, $user];
    }

    public function testCreateCompany(): void
    {
        [$client, $token, $user] = $this->createAuthenticatedClient();

        $client->request('POST', '/api/companies', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $token
        ], json_encode([
            'name' => 'Test Company',
            'address' => '123 Rue du Test',
            'city' => 'Testville',
            'postcode' => '00000'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Entreprise créée et utilisateur rattaché', $data['message'] ?? null);
    }

    public function testPromoteSuperAdmin(): void
    {
        [$client, $token, $adminUser] = $this->createAuthenticatedClient(true);

        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $company = new Company();
        $company->setName('PromoCorp');
        $company->setAddress('Rue Promo');
        $company->setCity('VillePromo');
        $company->setPostcode('12345');
        $em->persist($company);
        $adminUser->setCompany($company);
        $em->flush();

        $userToPromote = new User();
        $userToPromote->setEmail('future.superadmin' . uniqid() . '@example.com');
        $userToPromote->setPassword($passwordHasher->hashPassword($userToPromote, 'password123'));
        $userToPromote->setPhone('0123456789');
        $userToPromote->setCreatedDate(new \DateTimeImmutable());
        $userToPromote->setRoles(['ROLE_USER']);
        $userToPromote->setCompany($company);
        $em->persist($userToPromote);
        $em->flush();

        $client->request('PUT', '/api/companies/' . $company->getId() . '/superadmin', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $token
        ], json_encode([
            'user_id' => $userToPromote->getId()
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Utilisateur promu superadmin', $data['message'] ?? null);
    }
}
