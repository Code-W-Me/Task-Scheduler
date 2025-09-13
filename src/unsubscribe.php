<?php
require_once 'functions.php';

// TODO: Implement the unsubscription logic.
$message = "Could not process your request.";
if (isset($_GET['email'])) {
    $encoded_email = $_GET['email'];
    $email = base64_decode($encoded_email);
    if ($email && unsubscribeEmail($email)) {
        $message = "You have been successfully unsubscribed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<!-- Implement Header ! -->
	<title>Unsubscribe from Notifications</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; text-align: center; padding: 2rem; }
        .message-box { border: 1px solid #ccc; padding: 1rem 2rem; display: inline-block; border-radius: 8px; }
        .success { border-color: #28a745; background-color: #e9f7ea; color: #155724; }
        .error { border-color: #dc3545; background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
	<!-- Implemention body -->
	<div class="message-box <?php echo $message_class; ?>">
        <p><?php echo $message; ?></p>
    </div>
</body>
</html>
