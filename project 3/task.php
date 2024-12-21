<?php

define("TASKS_FILE", "tasks.json");

function loadTasks(): array
{
    if (!file_exists(TASKS_FILE)) {
        return [];
    }

    $data = file_get_contents(TASKS_FILE);

    return $data ? json_decode($data, true) : [];
}

$tasks = loadTasks();

function saveTasks(array $tasks): void
{
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task']) && !empty(trim($_POST['task']))) {

        $tasks[] = [
            'task' => htmlspecialchars(trim($_POST['task'])),
            'done' => false
        ];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['delete'])) {
        unset($tasks[$_POST['delete']]);
        $tasks = array_values($tasks);
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['toggle'])) {
        $tasks[$_POST['toggle']]['done'] = !$tasks[$_POST['toggle']]['done'];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<!-- ui -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>

    <style>
        body {
            margin: 20px;
            font-family: Arial, sans-serif;
        }

        .task-card {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background: #e0f7ff;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .task {
            color: #005580;
        }

        .task-done {
            text-decoration: line-through;
            color: #6c7b8a;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        button {
            cursor: pointer;
            padding: 5px 10px;
            border: 1px solid #0056b3;
            border-radius: 3px;
            background-color: #007BFF;
            color: white;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .button-outline {
            background-color: white;
            color: #007BFF;
            border: 1px solid #007BFF;
        }

        .button-outline:hover {
            background-color: #007BFF;
            color: white;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>

            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>

            <h2>Task List</h2>
            <ul style="list-style: none; padding: 0;">
                <?php if (empty($tasks)): ?>
                    <li>No tasks yet. Add one above!</li>
                <?php else: ?>
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task-item">
                            <form method="POST" style="flex-grow: 1;">
                                <input type="hidden" name="toggle" value="<?= $index ?>">

                                <button type="submit" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                                    <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                                        <?= htmlspecialchars($task['task']) ?>
                                    </span>
                                </button>
                            </form>

                            <form method="POST">
                                <input type="hidden" name="delete" value="<?= $index ?>">
                                <button type="submit" class="button button-outline">Delete</button>
                            </form>
                        </li>

                    <?php endforeach; ?>
                <?php endif; ?>

            </ul>

        </div>
    </div>
</body>

</html>