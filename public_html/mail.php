<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$emailApiKey = getenv('EMAIL_API_KEY');
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect post data
    
    $authKey = isset($_POST["api"]) ? trim($_POST["api"]) : null;
    
    if (!$authKey || $authKey !== $emailApiKey) {
        http_response_code(401);
        echo json_encode(["status" => "fail", "error" => "Unauthorized"]);
        exit;
    }

    $to = filter_var($_POST["to"], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST["subject"]);
    $message = htmlspecialchars($_POST["message"]);
    $headers = "From: noreply@letagentdo.xyz\r\n";
    $headers .= "Reply-To: noreply@letagentdo.xyz\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if ($to && $subject && $message) {
        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully!";
            echo $to;
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Invalid input.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Email</title>
</head>
<body>
    <h2>Send Email on Demand</h2>
    <form method="POST" action="">
        <label for="to">To:</label><br>
        <input type="email" name="to" required><br><br>

        <label for="subject">Subject:</label><br>
        <input type="text" name="subject" required><br><br>

        <label for="subject">API key:</label><br>
        <input type="text" name="api" required><br><br>

        <label for="message">Message:</label><br>
        <textarea name="message" rows="6" cols="40" required></textarea><br><br>

        <input type="submit" value="Send Email">
    </form>
</body>
</html>
