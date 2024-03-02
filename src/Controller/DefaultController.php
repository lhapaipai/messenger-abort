<?php

namespace App\Controller;

use App\Entity\Task;
use App\Enum\Status;
use App\Message\LongTaskMessage;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'welcome')]
    public function welcome(TaskRepository $taskRepository): Response
    {
        return $this->render('default/tasks.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/task/create', name: 'task_create')]
    public function createTask(MessageBusInterface $bus, EntityManagerInterface $entityManager): Response
    {
        $task = (new Task())
            ->setStatus(Status::Pending);

        $entityManager->persist($task);
        $entityManager->flush();

        $message = new LongTaskMessage($task->getId());
        $bus->dispatch($message);

        return $this->json($task);
    }

    #[Route('/task/{id}', name: 'task_view')]
    public function viewTask(Task $task): Response
    {
        return $this->json($task);
    }

    #[Route('/task/{id}/abort', name: 'task_abort')]
    public function abortTask(EntityManagerInterface $entityManager, Task $task): Response
    {
        if ($task && Status::Pending === $task->getStatus()) {
            posix_kill($task->getPid(), 9);
            $task->setStatus(Status::Aborted);
            $entityManager->flush();
        }

        return $this->json($task);
    }

    #[Route('/task/{id}/delete', name: 'task_delete')]
    public function deleteTask(EntityManagerInterface $entityManager, Task $task): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
