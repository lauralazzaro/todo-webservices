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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\Constants;

class AdminController extends AbstractController
{
    #[Route(Constants::ADMIN_USER_LIST_URL, name: Constants::ADMIN_USER_LIST_NAME)]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render(Constants::ADMIN_USER_LIST_VIEW, [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route(Constants::ADMIN_USER_CREATE_URL, name: Constants::ADMIN_USER_CREATE_NAME)]
    public function createAction(
        Request        $request,
        UserRepository $userRepository,
        UserHelper     $userHelper,
        Mailer         $mailer
    ): RedirectResponse|Response {
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

            $this->addFlash('success', "New user created. Waiting for validation");

            return $this->redirectToRoute(Constants::ADMIN_USER_LIST_NAME);
        }
        return $this->render(
            Constants::ADMIN_USER_CREATE_VIEW,
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route(Constants::ADMIN_USER_EDIT_URL, name: Constants::ADMIN_USER_EDIT_NAME)]
    public function editAction(
        User           $user,
        Request        $request,
        UserRepository $userRepository
    ): RedirectResponse|Response {
        $form = $this->createForm(AdminEditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            }

            $userRepository->save($user, true);

            $this->addFlash('success', "User successfully modified");

            return $this->redirectToRoute(Constants::ADMIN_USER_LIST_NAME);
        }
        return $this->render(
            Constants::ADMIN_USER_EDIT_VIEW,
            ['form' => $form->createView(),
                'user' => $user
            ]
        );
    }
}
