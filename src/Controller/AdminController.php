<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\Mailer;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class AdminController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/users",
        summary: "List all users",
        tags: ["Admin"],
        responses: [
        new OA\Response(
            response: 200,
            description: "List of users",
            content: new OA\JsonContent(
                type: "array",
                items: new OA\Items(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "username", type: "string"),
                        new OA\Property(property: "email", type: "string")
                    ]
                )
            )
        )
        ]
    )]
    #[Route('/admin/users', name: 'app_admin_user_list', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $userData = array_map(function ($user) {
            return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
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

            return new JsonResponse(
                [
                'message' => 'New user created. Waiting for validation'],
                Response::HTTP_CREATED
            );
        }

        return new JsonResponse(['error' => 'Invalid form data.'], Response::HTTP_BAD_REQUEST);
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

        return new JsonResponse(['error' => 'Invalid form data.'], Response::HTTP_BAD_REQUEST);
    }
}
