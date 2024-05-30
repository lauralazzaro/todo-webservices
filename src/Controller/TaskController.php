<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Helper\Utils;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Helper\Constants;

class TaskController extends AbstractController
{
    #[Route(Constants::TASK_CREATE_URL, name: Constants::TASK_CREATE_NAME)]
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

            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        return $this->render(Constants::TASK_CREATE_VIEW, ['form' => $form->createView()]);
    }

    #[Route(Constants::TASK_EDIT_URL, name: Constants::TASK_EDIT_NAME)]
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
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);
            $this->addFlash('success', 'The task has been modified.');
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        return $this->render(Constants::TASK_EDIT_VIEW, [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(Constants::TASK_TOGGLE_URL, name: Constants::TASK_TOGGLE_NAME)]
    public function toggleTaskAction(
        Task $task,
        TaskRepository $taskRepository
    ): RedirectResponse {
        $task->toggle();
        $this->addFlash('warning', 'The task has been successfully modified.');
        $taskRepository->save($task, true);
        return $this->redirectToRoute(Constants::TASK_LIST_NAME);
    }

    #[Route(Constants::TASK_DELETE_URL, name: Constants::TASK_DELETE_NAME)]
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
            return $this->redirectToRoute(Constants::TASK_LIST_NAME);
        }

        $taskRepository->remove($task, true);
        $this->addFlash('warning', 'The task has been successfully deleted.');
        return $this->redirectToRoute(Constants::TASK_LIST_NAME);
    }

    #[Route(
        Constants::TASK_LIST_URL,
        name: Constants::TASK_LIST_NAME,
        defaults: ['status' => Constants::TASK_STATUS_TODO, 'page' => 1]
    )]
    public function listTasks(
        string $status,
        int $page,
        TaskRepository $taskRepository
    ): Response {
        $pageSize = 5;
        $isDone = Utils::convertStatusToBool($status);
        $tasks = $taskRepository->findAllTasks($page, $pageSize, $isDone);

        $totalPages = ceil(count($tasks) / $pageSize);

        if ($page > $totalPages && $totalPages > 0) {
            $this->addFlash('warning', 'You tried to open a page that does not have any task.');
            return $this->redirectToRoute(Constants::TASK_LIST_NAME, [
                'status' => $status,
                'page' => $totalPages,
            ]);
        }

        return $this->render(Constants::TASK_LIST_VIEW, [
            'tasks' => $tasks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'status' => $status
        ]);
    }
}
