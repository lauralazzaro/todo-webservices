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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_user_list')]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @throws Exception
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route("/admin/users/create", name: "admin_user_create")]
    public function createAction(
        Request                     $request,
        UserRepository              $userRepository,
        UserHelper                  $userHelper,
        Mailer                      $mailer
    ): RedirectResponse|Response {
        $form = $this->createForm(AdminCreateUserType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            $newUser = $userHelper->initUserData($newUser);

            $userRepository->save($newUser, true);

            $mailer->sendEmail(
                $newUser->getEmail(),
                'inscription todo app',
                $newUser->getPassword()
            );


            $this->addFlash('success', "New user created. Waiting for validation");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/users/{id}/edit", name: "admin_user_edit")]
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
