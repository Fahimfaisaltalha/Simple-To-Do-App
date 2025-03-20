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
    header('Location: task.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .done {
            text-decoration: line-through;
            color: gray;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        input[type="text"] {
            padding: 10px;
            width: calc(100% - 90px);
            border: 1px solid #ccc;
            border-radius: 5px;
            color: black;
            background-color: white;
        }
        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
        }
        .task-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h2>To-Do List</h2>
    <form method="POST">
        <input type="text" name="task" placeholder="Enter a task" required>
        <button type="submit" name="action" value="add">Add</button>
    </form>
    <ul>
        <?php foreach ($tasks as $index => $task): ?>
            <li>
                <div class="task-container">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="index" value="<?= $index ?>">
                        <button type="submit" name="action" value="toggle" style="border:none;background:none;cursor:pointer;">
                            <span class="<?= $task['done'] ? 'done' : '' ?>" style="color: black;">
                                <?= ($index + 1) . '. ' . $task['text'] ?>
                            </span>
                        </button>
                    </form>
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="action" value="delete" class="delete-button">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
