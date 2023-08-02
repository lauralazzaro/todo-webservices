<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'task_list')]
    #[IsGranted(
        'ROLE_USER',
        message: 'You must be logged in',
        statusCode: 401
    )]
    public function listAction(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            ['tasks' => $taskRepository->findAll()]
        );
    }

    #[Route('/task/create', name: 'task_create')]
    #[IsGranted(
        'ROLE_USER',
        message: 'You must be logged in',
        statusCode: 401
    )]
    public function createAction(
        Request $request,
        TaskRepository $taskRepository
    ) {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task->setUser($user);

            $taskRepository->save($task, true);

            $this->addFlash('success', 'Task created successfully.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    #[IsGranted(
        TaskVoter::EDIT,
        'task',
        'You don\'t have the right to edit this task',
        401
    )]
    public function editAction(
        Task $task,
        Request $request,
        TaskRepository $taskRepository
    ) {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            $this->addFlash('success', 'The task has been modified.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    #[IsGranted(
        TaskVoter::EDIT,
        'task',
        'You don\'t have the right to edit this task',
        401
    )]
    public function toggleTaskAction(
        Task $task,
        TaskRepository $taskRepository
    ) {
        $task->toggle(!$task->isDone());
        $taskRepository->save($task, true);

        $this->addFlash('success', sprintf('The task %s has been modified.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted(
        TaskVoter::DELETE,
        'task',
        'You don\'t have the right to edit this task',
        401
    )]
    public function deleteTaskAction(
        Task $task,
        TaskRepository $taskRepository
    ) {
        $taskRepository->remove($task, true);

        $this->addFlash('success', 'The task has been successfully deleted.');

        return $this->redirectToRoute('task_list');
    }
}
