<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list')]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route("/users/create", name: "user_create")]
    public function createAction(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): RedirectResponse|Response {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $newUser,
                $newUser->getPassword()
            );
            $newUser->setPassword($hashedPassword);

            if (in_array('ROLE_ADMIN', $newUser->getRoles())) {
                $newUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            }
            $userRepository->save($newUser, true);

            $this->addFlash('success', "New user created.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/users/{id}/edit", name: "user_edit")]
    public function editAction(
        User $user,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $userRepository->save($user, true);

            $this->addFlash('success', "User successfully modified");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
