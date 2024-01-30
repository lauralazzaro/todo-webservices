<?php

namespace App\Controller;

use App\Form\AdminEditUserType;
use App\Form\UserEditType;
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

//    #[Route("/users/{id}/edit", name: "user_edit")]
//    public function editAction(
//        User                        $user,
//        Request                     $request,
//        UserPasswordHasherInterface $passwordHasher,
//        UserRepository              $userRepository
//    ): RedirectResponse|Response {
//        $form = $this->createForm(AdminPageEditUserType::class, $user);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            if (in_array('ROLE_ADMIN', $user->getRoles())) {
//                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
//            } else {
//                $user->setRoles(['ROLE_USER']);
//            }
//
//            $userRepository->save($user, true);
//
//            $this->addFlash('success', "User successfully modified");
//
//            return $this->redirectToRoute('user_list');
//        }
//        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
//    }
}
