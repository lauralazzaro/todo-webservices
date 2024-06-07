<?php

namespace App\Controller;

use App\Form\UserEditType;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Helper\Constants;

class UserController extends AbstractController
{
    #[Route(Constants::USER_EDIT_URL, name: Constants::USER_EDIT_NAME)]
    public function editAction(
        User $user,
        Request $request,
        UserRepository $userRepository,
        UserHelper $userHelper,
        int $id
    ): RedirectResponse|Response {
        try {
            $this->denyAccessUnlessGranted(
                attribute: UserVoter::EDIT,
                subject: $id,
                message: 'You don\'t have the rights to access this page.'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userHelper->updatePassword($user);
            $userRepository->save($user, true);
            $this->addFlash('success', "You successfully update your password");
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }
        return $this->render(Constants::USER_EDIT_VIEW, ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route(Constants::USER_GENERATED_PASSWORD_URL, name: Constants::USER_GENERATED_PASSWORD_NAME)]
    public function editGeneratedPasswordAction(
        User $user,
        Request $request,
        UserRepository $userRepository,
        UserHelper $userHelper,
        int $id
    ): RedirectResponse|Response {
        try {
            $this->denyAccessUnlessGranted(
                attribute: UserVoter::EDIT,
                subject: $id,
                message: 'You don\'t have the rights to access this page.'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userHelper->updatePassword($user);
            $userRepository->save($user, true);
            $this->addFlash('success', "You successfully create your password");
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }
        return $this->render(Constants::USER_GENERATED_PASSWORD_VIEW, ['form' => $form->createView(), 'user' => $user]);
    }
}
