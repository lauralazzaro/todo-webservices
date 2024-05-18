<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            ['tasks' => $taskRepository->findAll()]
        );
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(
        Request        $request,
        TaskRepository $taskRepository
    ): RedirectResponse|Response {
        $task = new Task();

        $user = $this->getUser();
        $task->setUser($user);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            $this->addFlash('success', 'Task created successfully.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(
        Task           $task,
        Request        $request,
        TaskRepository $taskRepository
    ): RedirectResponse|Response {
        try {
            $this->denyAccessUnlessGranted(
                attribute: TaskVoter::EDIT,
                subject: $task,
                message: 'You cannot edit this task.'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            $this->redirect('task_list');
        }

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
    public function toggleTaskAction(
        Task           $task,
        TaskRepository $taskRepository
    ) {
        $task->toggle(!$task->isDone());
        $taskRepository->save($task, true);
        $this->addFlash('success', sprintf('The task %s has been modified.', $task->getTitle()));
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(
        Task           $task,
        TaskRepository $taskRepository
    ): RedirectResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: TaskVoter::DELETE,
                subject: $task,
                message: 'You shall not pass!'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            $this->redirect('task_list');
        }
        
        $taskRepository->remove($task, true);
        $this->addFlash('success', 'The task has been successfully deleted.');
        return $this->redirectToRoute('task_list');
    }
}
