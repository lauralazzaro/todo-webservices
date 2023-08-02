<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_list')]
    #[IsGranted(
        UserVoter::VIEW,
        message: 'You don\'t have the rights to create a user',
        statusCode: 403
    )]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route("/users/create", name: "user_create")]
    #[IsGranted(
        UserVoter::CREATE,
        message: 'You don\'t have the rights to create a user',
        statusCode: 403
    )]
    public function createAction(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): RedirectResponse|Response {
        $newUser = new User();
        $form = $this->createForm(UserType::class, $newUser);

        $form->handleRequest($request);

        $password = $form->get('password')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $newUser,
                $password
            );

            $newUser->setPassword($hashedPassword);

            $userRepository->save($newUser, true);

            $this->addFlash('success', "User successfully created.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/users/{id}/edit", name: "user_edit")]
    #[IsGranted(
        UserVoter::EDIT,
        message: 'You don\'t have the right to edit this user',
        statusCode: 403
    )]
    public function editAction(
        User $user,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $loggedInUser = $this->getUser();

        if ($loggedInUser->getId() !== $user->getId()) {
            throw new AccessDeniedException("You are not allowed to edit this user.");
        }

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
