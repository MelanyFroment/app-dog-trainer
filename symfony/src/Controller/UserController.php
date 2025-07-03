<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{

    #[Route('/api/user', name: 'api_user_index', methods: ['GET'])]
    public function listAllUsers(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['message' => 'Accès interdit'], Response::HTTP_FORBIDDEN);
        }

        $users = $userRepository->findAll();

        $json = $serializer->serialize($users, 'json', ['groups' => 'user:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function me(UserInterface $user): JsonResponse
    {
        return $this->json([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/user/{id}', name: 'api_user_update', methods: ['PUT'])]
    public function updateUser(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour']);
    }


    #[Route('/api/user/me/clients', name: 'api_user_me_clients', methods: ['GET'])]
    public function getMyClients(Security $security): JsonResponse
    {
        $user = $security->getUser();
        $clients = $user->getClients();

        // Construire les données clients + chiens
        $data = [];

        foreach ($clients as $client) {
            $dogs = [];
            foreach ($client->getDogs() as $dog) {
                $dogs[] = [
                    'id' => $dog->getId(),
                    'name' => $dog->getName(),
                    'breed' => $dog->getBreed(),
                    'age' => $dog->getAge(),
                    'sex' => $dog->getSex(),
                    'weight' => $dog->getWeight(),
                    'vaccinated' => $dog->isVaccinated(),
                ];
            }

            $data[] = [
                'id' => $client->getId(),
                'firstname' => $client->getFirstname(),
                'lastname' => $client->getLastname(),
                'address' => $client->getAddress(),
                'postalCode' => $client->getPostalCode(),
                'city' => $client->getCity(),
                'phone' => $client->getPhone(),
                'profilPic' => $client->getProfilPic(),
                'dogs' => $dogs,
            ];
        }

        return new JsonResponse($data);
    }


}
