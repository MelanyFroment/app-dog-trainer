<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;


class UserController extends AbstractController
{
    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function me(UserInterface $user): JsonResponse
    {
        return $this->json([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
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
