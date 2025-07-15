<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SessionController extends AbstractController
{
    #[Route('/api/sessions', name: 'api_create_session', methods: ['POST'])]
    public function createSession(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $session = $serializer->deserialize($request->getContent(), Session::class, 'json');
        $session->setEducator($user);

        $em->persist($session);
        $em->flush();

        return new JsonResponse(['message' => 'Séance créée avec succès'], 201);
    }

    #[Route('/api/sessions', name: 'api_get_sessions', methods: ['GET'])]
    public function getSessions(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $sessions = $em->getRepository(Session::class)->findBy(['educator' => $user]);
        $json = $serializer->serialize($sessions, 'json', ['groups' => 'session:read']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/sessions/{id}', name: 'api_update_session', methods: ['PUT'])]
    public function updateSession(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) return new JsonResponse(['error' => 'Authentification requise'], 401);

        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || $session->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $serializer->deserialize($request->getContent(), Session::class, 'json', [
            'object_to_populate' => $session
        ]);

        $em->flush();
        return new JsonResponse(['message' => 'Séance mise à jour avec succès']);
    }

    #[Route('/api/sessions/{id}', name: 'api_delete_session', methods: ['DELETE'])]
    public function deleteSession(
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) return new JsonResponse(['error' => 'Authentification requise'], 401);

        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || $session->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $em->remove($session);
        $em->flush();

        return new JsonResponse(['message' => 'Séance supprimée avec succès']);
    }

    #[Route('/api/sessions/{id}/add-dogs', name: 'api_add_dogs_to_session', methods: ['PUT'])]
    public function addDogsToSession(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || $session->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $dogIds = $data['dog_ids'] ?? [];

        if (!is_array($dogIds) || count($dogIds) === 0) {
            return new JsonResponse(['error' => 'Liste de chiens requise'], 400);
        }

        foreach ($dogIds as $dogId) {
            $dog = $em->getRepository(\App\Entity\Dog::class)->find($dogId);

            if (!$dog) {
                return new JsonResponse(['error' => "Chien ID $dogId introuvable"], 404);
            }

            if ($dog->getOwner()->getEducator() !== $user) {
                return new JsonResponse(['error' => "Chien ID $dogId non autorisé"], 403);
            }

            $session->addDog($dog);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Chiens ajoutés à la séance avec succès']);
    }

    #[Route('/api/sessions/{sessionId}/remove-dog/{dogId}', name: 'api_remove_dog_from_session', methods: ['DELETE'])]
    public function removeDogFromSession(
        int $sessionId,
        int $dogId,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $session = $em->getRepository(\App\Entity\Session::class)->find($sessionId);
        $dog = $em->getRepository(\App\Entity\Dog::class)->find($dogId);

        if (!$session || $session->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Accès à la séance refusé'], 403);
        }

        if (!$dog || $dog->getOwner()->getEducator() !== $user) {
            return new JsonResponse(['error' => 'Chien non autorisé ou introuvable'], 403);
        }

        if (!$session->getDogs()->contains($dog)) {
            return new JsonResponse(['error' => 'Ce chien ne participe pas à cette séance'], 400);
        }

        $session->removeDog($dog);
        $em->flush();

        return new JsonResponse(['message' => 'Chien retiré de la séance avec succès']);
    }

}
