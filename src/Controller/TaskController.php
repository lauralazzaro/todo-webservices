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
                message: 'You cannot edit a task you did not create'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('task_list');
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

    /**
     * @throws \Exception
     */
    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(
        Task $task,
        TaskRepository $taskRepository,
        Request $request
    ): RedirectResponse {
        if (!$request->headers->get('referer')) {
            $this->addFlash('error', 'Please use the provided button to change the status of one task.');
            return $this->redirectToRoute('task_list');
        }

        $task->toggle();
        $this->addFlash('warning', 'The task has been successfully modified.');
        $taskRepository->save($task, true);
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(
        Task $task,
        TaskRepository $taskRepository,
        Request $request
    ): RedirectResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: TaskVoter::DELETE,
                subject: $task,
                message: 'You cannot delete a task you did not create'
            );
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('task_list');
        }

        if (!$request->headers->get('referer')) {
            $this->addFlash('error', 'Please use the provided button to delete one task.');
            return $this->redirectToRoute('task_list');
        }

        $taskRepository->remove($task, true);
        $this->addFlash('warning', 'The task has been successfully deleted.');
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{status}/{page}', name: 'task_list', defaults: ['status' => 'todo', 'page' => 1])]
    public function listTasks(
        string         $status,
        int            $page,
        TaskRepository $taskRepository
    ): Response {
        $pageSize = 5;
        if ($status === 'done') {
            $tasks = $taskRepository->findAllDoneWithPaginationAndOrder($page, $pageSize);
        } else {
            $tasks = $taskRepository->findAllToDoWithPaginationAndOrder($page, $pageSize);
        }

        $totalPages = ceil(count($tasks) / $pageSize);

        if ($page > $totalPages && $totalPages > 0) {
            $this->addFlash('warning', 'You tried to open a page that does not have any task.');
            return $this->redirectToRoute('task_list', [
                'status' => $status,
                'page' => $totalPages,
            ]);
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'status' => $status
        ]);
    }
}
