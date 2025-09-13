<?php

// Define the base URL for the application.
define('BASE_URL', 'http://localhost:8000');

/* Adding the Helper function to read and write or decode the Json From a File 
also using the @param for string $filepath The path to the JSON file  and @return to decoded JSON Data as an aassociative array
*/
function _read_json(string $filepath): array {
    if (!file_exists($filepath) || filesize($filepath) === 0) {
        return [];
    }
    $json_data = file_get_contents($filepath);
    return json_decode($json_data, true) ?: [];
}

/**  Uses the second helper function to encode data to JSON and write to a file. @param string $filepath The path to the file @param array $data The data to encode and write.
 */
function _write_json(string $filepath, array $data): void {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filepath, $json_data);
}





/**
 * Adds a new task to the task list
 * 
 * @param string $task_name The name of the task to add.
 * @return bool True on success, false on failure.
 */


function addTask( string $task_name ): bool {
    $file  = __DIR__ . '/tasks.txt';
    $tasks = getAllTasks();

    
    $newTaskName = strtolower(trim($task_name));

    
    foreach ($tasks as $task) {
        $existingTaskName = strtolower(trim($task['name']));

        if ($existingTaskName === $newTaskName) {
            // A match was found. This is a duplicate. Stop the function.
            return false;
        }
    }

    // If the loop finishes without finding a match, add the new task.
    $new_task = [
        'id' => uniqid('task_'),
        'name' => trim($task_name),
        'completed' => false
    ];

    $tasks[] = $new_task;
    _write_json($file, $tasks);
    
    return true;
}



/**
 * Retrieves all tasks from the tasks.txt file
 * 
 * @return array Array of tasks. -- Format [ id, name, completed ]
 */
function getAllTasks(): array {
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	return _read_json($file);
}

/**
 * Marks a task as completed or uncompleted
 * 
 * @param string  $task_id The ID of the task to mark.
 * @param bool $is_completed True to mark as completed, false to mark as uncompleted.
 * @return bool True on success, false on failure
 */
function markTaskAsCompleted( string $task_id, bool $is_completed ): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = getAllTasks();
    $task_found = false;

    // The '&' here is CRITICAL. It means "modify the actual task, not a copy".
    foreach ($tasks as &$task) { 
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            $task_found = true;
            break;
        }
    }
    unset($task); 

    if ($task_found) {
        _write_json($file, $tasks);
        return true;
    }
    return false;
}

/**
 * Deletes a task from the task list
 * 
 * @param string $task_id The ID of the task to delete.
 * @return bool True on success, false on failure.
 */
function deleteTask( string $task_id ): bool {
	$file  = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	$tasks = getAllTasks();
    $tasks_to_keep = [];

    foreach ($tasks as $task) {
        if ($task['id'] !== $task_id) {
            $tasks_to_keep[] = $task;
        }
    }

    // If the number of tasks changed, it means we found and removed it.
    if (count($tasks) > count($tasks_to_keep)) {
        _write_json($file, $tasks_to_keep);
        return true;
    }
    return false;
}

/**
 * Generates a 6-digit verification code
 * 
 * @return string The generated verification code.
 */
function generateVerificationCode(): string {
	// TODO: Implement this function
	return strval(random_int(100000, 999999));
}

/**
 * Subscribe an email address to task notifications.
 *
 * Generates a verification code, stores the pending subscription,
 * and sends a verification email to the subscriber.
 *
 * @param string $email The email address to subscribe.
 * @return bool True if verification email sent successfully, false otherwise.
 */


function subscribeEmail( string $email ): bool {
    $file = __DIR__ . '/pending_subscriptions.txt';
    $pending_subs = _read_json($file);

    $verification_code = generateVerificationCode();
    $verification_link = BASE_URL . "/verify.php?email=" . urlencode($email) . "&code=$verification_code";

    $pending_subs[$email] = [
        'code' => $verification_code,
        'timestamp' => time()
    ];
    _write_json($file, $pending_subs);

    $subject = 'Verify subscription to Task Planner';
    $body = "<p>Click the link below to verify your subscription to Task Planner:</p>";
    $body .= "<p><a id=\"verification-link\" href=\"$verification_link\">Verify Subscription</a></p>";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: no-reply@example.com'
    ];

    // We join the array elements together with the correct line ending.
    $header_string = implode("\r\n", $headers);

    // Pass the final header string to the mail() function.
    return mail($email, $subject, $body, $header_string);
}

/**
 * Verifies an email subscription
 * 
 * @param string $email The email address to verify.
 * @param string $code The verification code.
 * @return bool True on success, false on failure.
 */
function verifySubscription( string $email, string $code ): bool {
    $pending_file     = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';

    $pending_subs = _read_json($pending_file);

    // Check if email exists and code matches
    if (isset($pending_subs[$email]) && $pending_subs[$email]['code'] === $code) {
        
    
        $subscribers = _read_json($subscribers_file);
        if (!in_array($email, $subscribers)) {
            $subscribers[] = $email;
            _write_json($subscribers_file, $subscribers);
        }

        
        
        // Use unset() to remove the email key from the pending array
        unset($pending_subs[$email]);
        
        // Write the newly-modified pending array back to the file
        _write_json($pending_file, $pending_subs);

        return true;
    }
    
    return false;
}

/**
 * Unsubscribes an email from the subscribers list
 * 
 * @param string $email The email address to unsubscribe.
 * @return bool True on success, false on failure.
 */
function unsubscribeEmail( string $email ): bool {
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	$subscribers = _read_json($subscribers_file);
    
    $subscribers_to_keep = [];
    foreach ($subscribers as $subscriber) {
        if ($subscriber !== $email) {
            $subscribers_to_keep[] = $subscriber;
        }
    }

    if (count($subscribers) > count($subscribers_to_keep)) {
        _write_json($subscribers_file, $subscribers_to_keep);
        return true;
    }
    return false;
}

/**
 * Sends task reminders to all subscribers
 * Internally calls  sendTaskEmail() for each subscriber
 */
function sendTaskReminders(): void {
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	$subscribers = _read_json($subscribers_file);
    $all_tasks = getAllTasks();

    // Filter for pending tasks
    $pending_tasks = [];
    foreach ($all_tasks as $task) {
        if (!$task['completed']) {
            $pending_tasks[] = $task;
        }
    }

    // If there are no pending tasks, do nothing
    if (empty($pending_tasks)) {
        return;
    }

    // Send email to each subscriber
    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

/**
 * Sends a task reminder email to a subscriber with pending tasks.
 *
 * @param string $email The email address of the subscriber.
 * @param array $pending_tasks Array of pending tasks to include in the email.
 * @return bool True if email was sent successfully, false otherwise.
 */
function sendTaskEmail( string $email, array $pending_tasks ): bool {
	$subject = 'Task Planner - Pending Tasks Reminder';
	// TODO: Implement this function
	$unsubscribe_link = BASE_URL . "/unsubscribe.php?email=" . urlencode(base64_encode($email));

    // Build the HTML body
    $body = '<h2>Pending Tasks Reminder</h2>';
    $body .= '<p>Here are the current pending tasks:</p>';
    $body .= '<ul>';
    foreach ($pending_tasks as $task) {
        $body .= '<li>' . htmlspecialchars($task['name']) . '</li>';
    }
    $body .= '</ul>';
    $body .= "<p><a id=\"unsubscribe-link\" href=\"$unsubscribe_link\">Unsubscribe from notifications</a></p>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <no-reply@example.com>' . "\r\n";

    return mail($email, $subject, $body, $headers);
}
