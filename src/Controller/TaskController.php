<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskResponseMessage;
use App\Enum\TaskStatus;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OpenApi\Attributes as OA;

class TaskController extends AbstractController
{
    #[OA\Post(
        path: "/tasks/create",
        summary: "Create a new task",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/Task")
        ),
        tags: ["Tasks"],
        responses: [
            new OA\Response(
                response: 201,
                description: TaskResponseMessage::TASK_SAVED->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: TaskResponseMessage::TASK_SAVED
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: TaskResponseMessage::INVALID_FORM_SUBMISSION->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INVALID_FORM_SUBMISSION
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INTERNAL_SERVER_ERROR
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[Route('/tasks/create', name: 'api_task_create', methods: ['POST'])]
    public function createAction(
        Request $request,
        TaskRepository $taskRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        try {
            $data = $request->getContent();
            $task = $serializer->deserialize($data, Task::class, 'json');

            $userId = json_decode($data, true)['user']['id'];
            $user = $userRepository->find($userId);
            if (!$user instanceof User) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $task->setUser($user);

            $taskRepository->save($task, true);
            return new JsonResponse(
                $serializer->serialize(
                    $task,
                    'json',
                    ['groups' => 'task_list']
                ),
                Response::HTTP_CREATED,
                [],
                true
            );
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Put(
        path: "/tasks/{id}/edit",
        summary: "Edit an existing task",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/Task")
        ),
        tags: ["Tasks"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: TaskResponseMessage::TASK_MODIFIED->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: TaskResponseMessage::TASK_MODIFIED
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: TaskResponseMessage::INVALID_FORM_SUBMISSION->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INVALID_FORM_SUBMISSION
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: TaskResponseMessage::INTERNAL_SERVER_ERROR->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INTERNAL_SERVER_ERROR
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[Route('/tasks/{id}/edit', name: 'api_task_edit', methods: ['PUT'])]
    public function editAction(
        Task $task,
        Request $request,
        TaskRepository $taskRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: TaskVoter::EDIT,
                subject: $task,
                message: 'You cannot edit a task you did not create'
            );
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        try {
            $data = json_decode($request->getContent(), true);
            $task = $serializer->deserialize(
                $request->getContent(),
                Task::class,
                'json',
                ['object_to_populate' => $task]
            );

            if (isset($data['title'])) {
                $task->setTitle($data['title']);
            }
            if (isset($data['content'])) {
                $task->setContent($data['content']);
            }
            if (isset($data['deadline'])) {
                $task->setDeadline(new DateTime($data['deadline']));
            }

            $taskRepository->save($task, true);

            return new JsonResponse(
                $serializer->serialize(
                    $task,
                    'json',
                    ['groups' => 'task_list']
                ),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Put(
        path: "/tasks/{id}/change-status/{status}",
        summary: "Change the status of a task",
        tags: ["Tasks"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "status",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task status modified successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "The task status has been successfully modified."
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INTERNAL_SERVER_ERROR
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[Route('/tasks/{id}/change-status/{status}', name: 'api_task_change_status', methods: ['PUT'])]
    public function changeStatusTaskAction(
        Task $task,
        TaskRepository $taskRepository,
        TaskStatus $status
    ): JsonResponse {
        try {
            $task->setStatus($status);
            $taskRepository->save($task, true);
            return new JsonResponse(['message' => TaskResponseMessage::TASK_STATUS_MODIFIED], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: "/tasks/{id}/delete",
        summary: "Delete a task",
        tags: ["Tasks"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: TaskResponseMessage::TASK_DELETED->value,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: TaskResponseMessage::TASK_DELETED
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: TaskResponseMessage::INTERNAL_SERVER_ERROR
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['DELETE'])]
    public function deleteTaskAction(
        Task $task,
        TaskRepository $taskRepository
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(
                attribute: TaskVoter::DELETE,
                subject: $task,
                message: 'You cannot delete a task you did not create'
            );
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $task->setDeletedAt(new DateTime());
            $taskRepository->save($task, true);
            return new JsonResponse(['message' => TaskResponseMessage::TASK_DELETED], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/tasks",
        summary: "List all tasks",
        tags: ["Tasks"],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Task")
                )
            )
        ]
    )]
    #[Route('/tasks', name: 'api_task_list', methods: ['GET'])]
    public function listTasks(
        Request $request,
        TaskRepository $taskRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $currentPage = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $tasks = $taskRepository->findAllTasks($currentPage, $limit);
        $tasksJson = $serializer->serialize($tasks, 'json', ['groups' => 'task_list']);
        return new JsonResponse($tasksJson, Response::HTTP_OK, [], true);
    }
}
