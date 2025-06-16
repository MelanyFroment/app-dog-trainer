<?php

namespace App\Controller;

use App\Entity\Dog;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DogController extends AbstractController
{
    #[Route('/api/dogs', name: 'api_create_dog', methods: ['POST'])]
    public function createDog(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $clientId = $data['owner_id'] ?? null;

        if (!$clientId) {
            return new JsonResponse(['error' => 'Champ "owner_id" requis'], 400);
        }

        $client = $em->getRepository(Client::class)->find($clientId);
        if (!$client || $client->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Client invalide ou non autorisé'], 403);
        }

        $dog = $serializer->deserialize($request->getContent(), Dog::class, 'json');
        $dog->setOwner($client);

        $em->persist($dog);
        $em->flush();

        return new JsonResponse(['message' => 'Chien créé avec succès'], 201);
    }

    #[Route('/api/dogs', name: 'api_get_dogs', methods: ['GET'])]
    public function getDogs(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $clients = $em->getRepository(Client::class)->findBy(['educator' => $user]);
        $dogs = [];

        foreach ($clients as $client) {
            foreach ($client->getDogs() as $dog) {
                $dogs[] = $dog;
            }
        }

        $json = $serializer->serialize($dogs, 'json', ['groups' => 'dog:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/dogs/{id}', name: 'api_update_dog', methods: ['PUT'])]
    public function updateDog(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Authentification requise'], 401);

        $dog = $em->getRepository(Dog::class)->find($id);
        if (!$dog || $dog->getOwner()->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $serializer->deserialize($request->getContent(), Dog::class, 'json', [
            'object_to_populate' => $dog
        ]);

        $em->flush();
        return new JsonResponse(['message' => 'Chien mis à jour avec succès']);
    }

    #[Route('/api/dogs/{id}', name: 'api_delete_dog', methods: ['DELETE'])]
    public function deleteDog(
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Authentification requise'], 401);

        $dog = $em->getRepository(Dog::class)->find($id);
        if (!$dog || $dog->getOwner()->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $em->remove($dog);
        $em->flush();

        return new JsonResponse(['message' => 'Chien supprimé avec succès']);
    }
}
