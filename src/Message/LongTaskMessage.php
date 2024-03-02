<?php

namespace App\Message;

final class LongTaskMessage
{
    public function __construct(private string $taskId)
    {
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }
}
