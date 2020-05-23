<?php

namespace TaskService\Serializer;

use TaskService\Models\Task;

class TasksSerializer
{
    /**
     * @param Task[] $tasks
     */
    public function serializeTasks(array $tasks): array
    {
        $result = [];

        foreach ($tasks as $task) {
            $result[] = $this->serializeTask($task);
        }

        return $result;
    }

    public function serializeTask(Task $task): array
    {
        return [
            'id' => (int) $task->id,
            'title' => $task->title,
            'duedate' => $task->duedate,
            'completed' => (bool) $task->completed,
        ];
    }
}
