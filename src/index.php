<?php
require_once 'functions.php';

// Handle all form submissions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logic for adding a new task
    if (isset($_POST['add-task-action']) && !empty($_POST['task-name'])) {
        addTask($_POST['task-name']);
    }

    // Logic for deleting a task
    if (isset($_POST['delete-task-action'])) {
        deleteTask($_POST['task_id']);
    }

    // Logic for updating a task's completion status
    if (isset($_POST['update-status-action'])) {
        $is_completed = isset($_POST['task_status']);
        markTaskAsCompleted($_POST['task_id'], $is_completed);
    }

    // Logic for email subscription
    if (isset($_POST['subscribe-action']) && !empty($_POST['email'])) {
        subscribeEmail($_POST['email']);
    }

    // Redirect to prevent re-submission on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all tasks to display on the page
$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            max-width: 700px;
            margin: 2rem auto;
            padding: 1rem;
        }
        h1, h2 {
            color: #2c3e50;
            text-align: center;
        }
        form {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        input[type="text"], input[type="email"] {
            width: calc(100% - 110px);
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 0.5rem;
        }
        button {
            padding: 0.75rem 1.5rem;
            border: none;
            background-color: #3498db;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .tasks-list {
            list-style: none;
            padding: 0;
        }
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 2rem;

        }
        .task-item.completed span {
            text-decoration: line-through;
            color: #95a5a6;
        }
        .task-content-form {
            display: flex;
            align-items: center;
            flex-grow: 1;
            padding: 0; margin: 0; box-shadow: none;
        }
        .task-delete-form {
             padding: 0; margin: 0; box-shadow: none;
        }
        .task-status {
            margin-right: 1rem;
            min-width: 16px;
            min-height: 16px;
        }
        .delete-task {
            background-color: #e74c3c;
        }
        .delete-task:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Task Manager</h1>

    
    <form method="POST" action="">
        <input type="hidden" name="add-task-action" value="1">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>

    <ul class="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                <form method="POST" action="" class="task-content-form">
                    <input type="hidden" name="update-status-action" value="1">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                    <input type="checkbox" class="task-status" name="task_status" onchange="this.form.submit()" <?php echo $task['completed'] ? 'checked' : ''; ?>>
                    <span><?php echo htmlspecialchars($task['name']); ?></span>
                </form>
                <form method="POST" action="" class="task-delete-form">
                    <input type="hidden" name="delete-task-action" value="1">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                    <button type="submit" class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Subscribe for Reminders</h2>
    <form method="POST" action="">
        <input type="hidden" name="subscribe-action" value="1">
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <button type="submit" id="submit-email">Subscribe</button>
    </form>
</body>
</html>