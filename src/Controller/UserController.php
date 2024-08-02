<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends AbstractController
{
    #[Route('/api/user/{id}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function editAction(
        User $user,
        Request $request,
        UserRepository $userRepository,
        UserHelper $userHelper,
        int $id
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: UserVoter::EDIT,
                subject: $id,
                message: 'You don\'t have the rights to access this page.'
            );
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userHelper->updatePassword($user);
            $userRepository->save($user, true);
            return new JsonResponse(['message' => 'You successfully updated your password'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Invalid form data.'], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/users/{id}/edit/generated_password', name: 'app_user_edit_generated_password', methods: ['PUT'])]
    public function editGeneratedPasswordAction(
        User $user,
        Request $request,
        UserRepository $userRepository,
        UserHelper $userHelper,
        int $id
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: UserVoter::EDIT,
                subject: $id,
                message: 'You don\'t have the rights to access this page.'
            );
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userHelper->updatePassword($user);
            $userRepository->save($user, true);
            return new JsonResponse(['message' => 'You successfully created your password'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Invalid form data.'], JsonResponse::HTTP_BAD_REQUEST);
    }
}