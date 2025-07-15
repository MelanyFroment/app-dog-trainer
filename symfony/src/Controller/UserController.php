<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function me(UserInterface $user): JsonResponse
    {
        $company = $user->getCompany();
        return $this->json([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'isCompanySuperAdmin' => method_exists($user, 'isCompanySuperAdmin') ? $user->isCompanySuperAdmin() : false,
            'company' => $company ? [
                'id' => $company->getId(),
                'name' => $company->getName()
            ] : null,
        ]);
    }

    #[Route('/api/user', name: 'api_get_all_users', methods: ['GET'])]
    public function getAllUsers(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $companyId = $request->query->get('company');

        $repo = $em->getRepository(\App\Entity\User::class);

        $users = $companyId
            ? $repo->findBy(['company' => $companyId])
            : $repo->findAll();

        $data = [];

        foreach ($users as $user) {
            $company = $user->getCompany();
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'isCompanySuperAdmin' => $user->isCompanySuperAdmin(),
                'company' => $company ? [
                    'id' => $company->getId(),
                    'name' => $company->getName()
                ] : null
            ];
        }

        return new JsonResponse($data);
    }

}
