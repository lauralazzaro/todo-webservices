<?php

namespace App\Controller;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Form\TaskType;
use App\Helper\Utils;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use DateTime;
use Exception;
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
        Request $request,
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
        Task $task,
        Request $request,
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
     * @throws Exception
     */
    #[Route('/tasks/{id}/change-status/{status}', name: 'task_change_status')]
    public function changeStatusTaskAction(
        Task $task,
        TaskRepository $taskRepository,
        TaskStatus $status
    ): RedirectResponse {
        $task->setStatus($status);
        $this->addFlash('warning', 'The task has been successfully modified.');
        $taskRepository->save($task, true);
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(
        Task $task,
        TaskRepository $taskRepository
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

        $task->setDeletedAt(new DateTime());
        $taskRepository->save($task, true);
        $this->addFlash('warning', 'The task has been successfully deleted.');
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{page}', name: 'task_list', defaults: ['page' => 1])]
    public function listTasks(
        Request $request,
        TaskRepository $taskRepository
    ): Response {
        $sort = $request->query->get('sort', 'title');
        $direction = $request->query->get('direction', 'asc');
        $currentPage = $request->query->getInt('page', 1);

        $tasks = $taskRepository->findBy([], [$sort => $direction], $limit = 10, $offset = ($currentPage - 1) * 10);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'currentPage' => $currentPage,
            'totalPages' => ceil(count($tasks) / 10),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
