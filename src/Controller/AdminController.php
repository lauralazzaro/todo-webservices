<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminCreateUserType;
use App\Form\AdminEditUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route("/users/create", name: "user_create")]
    public function createAction(
        Request                     $request,
        UserRepository              $userRepository,
        TokenGeneratorInterface     $tokenGenerator
    ): RedirectResponse|Response {
        $form = $this->createForm(AdminCreateUserType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            /*
             * INIT VALUES
             *
             * - when an admin creates a user, the username will be the mail and after that the user can modify it
             * - first the user is not validated
             * - set the expiration of the token at +48 hours
             * - generate a random token
             */
            $newUser->setUsername($newUser->getEmail());
            $newUser->setIsValidated(false);
            $newUser->setVerificationTokenExpirationDate();
            $newUser->setVerificationToken($tokenGenerator->generateToken());

            if (in_array('ROLE_ADMIN', $newUser->getRoles())) {
                $newUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $newUser->setRoles(['ROLE_USER']);
            }
            $userRepository->save($newUser, true);

            //send email to user


            $this->addFlash('success', "New user created. Waiting for validation");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/users/{id}/edit", name: "user_edit")]
    public function editAction(
        User                        $user,
        Request                     $request,
        UserRepository              $userRepository
    ): RedirectResponse|Response {
        $form = $this->createForm(AdminEditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            $userRepository->save($user, true);

            $this->addFlash('success', "User successfully modified");

            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
