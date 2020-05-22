<?php

namespace TaskService\Tests\Unit\Serializer;

use PHPUnit\Framework\TestCase;
use TaskService\Models\Task;
use TaskService\Serializer\TasksSerializer;

class TasksSerializerTest extends TestCase
{
    public function testSerializeTasks(): void
    {
        $task = new Task();
        $task->id = 1234;
        $task->title = 'test task';
        $task->duedate = '2020-05-22';
        $task->completed = false;

        $serializer = new TasksSerializer();

        $actual = $serializer->serializeTasks([$task]);

        $expected = [[
            'id' => 1234,
            'title' => 'test task',
            'duedate' => '2020-05-22',
            'completed' => false,
        ]];

        $this->assertEquals($expected, $actual);
    }
}
