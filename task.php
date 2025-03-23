<?php

// Define the JSON file
$tasksFile = 'tasks.json';

// Load existing tasks
$tasks = file_exists($tasksFile) ? json_decode(file_get_contents($tasksFile), true) : [];
if (!is_array($tasks)) {
    $tasks = [];
}

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' && !empty(trim($_POST['task']))) {
            $tasks[] = [
                'text' => htmlspecialchars($_POST['task']),
                'done' => false
            ];
        }

        if ($action === 'toggle' && isset($_POST['index'])) {
            $index = $_POST['index'];
            $tasks[$index]['done'] = !$tasks[$index]['done'];
        }

        if ($action === 'delete' && isset($_POST['index'])) {
            $index = $_POST['index'];
            array_splice($tasks, $index, 1);
        }

        // Save the tasks back to the file
        file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
    
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body {
            margin-top: 20px;
        }
        .task-card {
            border: 1px solid #ececec; 
            padding: 20px;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .task {
            color: #888;
        }
        .task-done {
            text-decoration: line-through;
            color: #888;
        }
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        ul {
            padding-left: 20px;
        }
        button {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>

            <!-- Add Task Form -->
            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" name="action" value="add" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>

            <!-- Task List -->
            <h2>Task List</h2>
            <ul style="list-style: none; padding: 0;">
                <?php if (empty($tasks)): ?>
                    <li>No tasks yet. Add one above!</li>
                <?php else: ?>
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task-item">
                            <form method="POST" style="flex-grow: 1;">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="action" value="toggle" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                                    <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                                        <?= ($index + 1) . ". " . htmlspecialchars($task['text']) ?> 
                                    </span>
                                </button>
                            </form>

                            <form method="POST">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="action" value="delete" class="button button-outline" style="margin-left: 10px;">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>