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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route("/users/{id}/edit", name: "user_edit")]
    public function editAction(
        User                        $user,
        Request                     $request,
        UserRepository              $userRepository,
        UserHelper                  $userHelper,
        int $id
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(
            attribute: UserVoter::EDIT,
            subject: $id,
            message: 'You shall not pass!'
        );

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userHelper->updatePassword($user);

            $userRepository->save($user, true);

            $this->addFlash('success', "User successfully modified");

            return $this->redirectToRoute('task_list');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
