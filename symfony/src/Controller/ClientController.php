<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{
    #[Route('/api/clients', name: 'api_create_client', methods: ['POST'])]
    public function createClient(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');
        $client->setEducator($user);

        $em->persist($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client créé avec succès'], 201);
    }

    #[Route('/api/clients', name: 'api_get_clients', methods: ['GET'])]
    public function getClients(
        EntityManagerInterface $em,
        Security $security,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $clients = $em->getRepository(Client::class)->findBy(['educator' => $user]);

        $json = $serializer->serialize($clients, 'json', ['groups' => 'client:read']);

        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/api/clients/{id}', name: 'api_update_client', methods: ['PUT'])]
    public function updateClient(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $client = $em->getRepository(Client::class)->find($id);

        if (!$client || $client->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $serializer->deserialize(
                $request->getContent(),
                Client::class,
                'json',
                ['object_to_populate' => $client]
            );
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur de désérialisation',
                'details' => $e->getMessage()
            ], 400);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Client mis à jour avec succès'], 200);
    }



    #[Route('/api/clients/{id}', name: 'api_delete_client', methods: ['DELETE'])]
    public function deleteClient(
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $client = $em->getRepository(Client::class)->find($id);

        if (!$client || $client->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $em->remove($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client supprimé avec succès']);
    }

}