<?php

namespace App\MessageHandler;

use App\Enum\Status;
use App\Message\LongTaskMessage;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LongTaskMessageHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(LongTaskMessage $message)
    {
        $task = $this->taskRepository->find($message->getTaskId());

        $pid = getmypid();

        $task->setPid($pid);
        $this->entityManager->flush();

        sleep(5);

        if (!$task) {
            return;
        }

        $task->setStatus(Status::Success);
        $task->setResult('result for task');
        $this->entityManager->flush();
    }
}
