<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminCreateUserType;
use App\Form\AdminEditUserType;
use App\Helper\Mailer;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/api/admin/users', name: 'app_admin_user_list', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $userData = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                // Add other user fields as needed
            ];
        }, $users);

        return new JsonResponse(['users' => $userData]);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route('/api/admin/users/create', name: 'app_admin_user_create', methods: ['POST'])]
    public function createAction(
        Request $request,
        UserRepository $userRepository,
        UserHelper $userHelper,
        Mailer $mailer
    ): JsonResponse {
        $form = $this->createForm(AdminCreateUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();
            $initUserData = $userHelper->initUserData($newUser);
            $userRepository->save($initUserData['user'], true);

            $mailer->sendEmail(
                "Temporary password for your ToDo App account",
                $initUserData['plainPassword'],
                $newUser->getEmail()
            );

            return new JsonResponse(['message' => 'New user created. Waiting for validation'], JsonResponse::HTTP_CREATED);
        }

        return new JsonResponse(['error' => 'Invalid form data.'], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/admin/users/{id}/edit', name: 'app_admin_user_edit', methods: ['PUT'])]
    public function editAction(
        User $user,
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        $form = $this->createForm(AdminEditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            return new JsonResponse(['message' => 'User successfully modified']);
        }

        return new JsonResponse(['error' => 'Invalid form data.'], JsonResponse::HTTP_BAD_REQUEST);
    }
}