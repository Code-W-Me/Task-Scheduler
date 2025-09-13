<?php
require_once 'functions.php';

// This message will be displayed to the user.
$message = 'Invalid verification link. The code may have been incorrect or expired.';
$message_class = 'error'; // For styling

// Check if the email and code are present in the URL
if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];

    // Attempt to verify the subscription by calling the function
    if (verifySubscription($email, $code)) {
        $message = 'Thank you! Your email has been successfully verified. You will now receive task reminders.';
        $message_class = 'success';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Verification</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; text-align: center; padding: 2rem; background-color: #f4f7f6; }
        h2 { color: #2c3e50; }
        .message-box { border: 1px solid #ccc; padding: 1rem 2rem; display: inline-block; border-radius: 8px; margin-top: 1rem; }
        .success { border-color: #28a745; background-color: #e9f7ea; color: #155724; }
        .error { border-color: #dc3545; background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h2 id="verification-heading">Subscription Verification</h2>
    
    <div class="message-box <?php echo $message_class; ?>">
        <p><?php echo $message; ?></p>
    </div>

</body>
</html>