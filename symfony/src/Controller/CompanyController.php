<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CompanyController extends AbstractController
{
    #[Route('/api/companies', name: 'api_create_company', methods: ['POST'])]
    public function createCompany(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Authentification requise'], 401);
        }

        $company = $serializer->deserialize($request->getContent(), Company::class, 'json');

        $em->persist($company);

        // L'utilisateur créateur devient superadmin de la société
        $user->setCompany($company);
        $user->setIsCompanySuperAdmin(true);
        $em->flush();

        return new JsonResponse(['message' => 'Entreprise créée et utilisateur rattaché'], 201);
    }

    #[Route('/api/companies', name: 'api_get_companies', methods: ['GET'])]
    public function getCompanies(
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $companies = $em->getRepository(Company::class)->findAll();
        $json = $serializer->serialize($companies, 'json', ['groups' => 'company:read']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/companies/{id}', name: 'api_update_company', methods: ['PUT'])]
    public function updateCompany(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        $company = $em->getRepository(Company::class)->find($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Entreprise introuvable'], 404);
        }

        if (!$this->isGranted('ROLE_ADMIN') &&
            (!$user || $user->getCompany()?->getId() !== $company->getId() || !$user->isCompanySuperAdmin())) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $serializer->deserialize($request->getContent(), Company::class, 'json', [
            'object_to_populate' => $company
        ]);

        $em->flush();

        return new JsonResponse(['message' => 'Entreprise mise à jour avec succès']);
    }

    #[Route('/api/companies/{id}', name: 'api_delete_company', methods: ['DELETE'])]
    public function deleteCompany(
        int $id,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        $company = $em->getRepository(Company::class)->find($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Entreprise introuvable'], 404);
        }

        if (!$this->isGranted('ROLE_ADMIN') &&
            (!$user || $user->getCompany()?->getId() !== $company->getId() || !$user->isCompanySuperAdmin())) {
            return new JsonResponse(['error' => 'Suppression non autorisée'], 403);
        }

        $em->remove($company);
        $em->flush();

        return new JsonResponse(['message' => 'Entreprise supprimée avec succès']);
    }

    #[Route('/api/companies/{id}/superadmin', name: 'api_change_superadmin', methods: ['PUT'])]
    public function addSuperAdmin(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        $newUserId = $data['user_id'] ?? null;

        if (!$newUserId) {
            return new JsonResponse(['error' => 'Paramètre user_id requis'], 400);
        }

        $company = $em->getRepository(Company::class)->find($id);
        if (!$company) {
            return new JsonResponse(['error' => 'Entreprise introuvable'], 404);
        }

        $user = $em->getRepository(User::class)->find($newUserId);
        if (!$user || $user->getCompany()?->getId() !== $company->getId()) {
            return new JsonResponse(['error' => 'Utilisateur non valide pour cette entreprise'], 403);
        }

        if ($user->isCompanySuperAdmin() && $user->getCompany()->getId() !== $company->getId()) {
            return new JsonResponse(['error' => 'Utilisateur déjà superadmin d’une autre entreprise'], 403);
        }

        $user->setIsCompanySuperAdmin(true);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur promu superadmin']);
    }



}
