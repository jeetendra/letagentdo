<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    $emailApiKey = getenv('EMAIL_API_KEY');
    $host = getenv('HOST');
    
    $authKey = isset($_POST["api"]) ? trim($_POST["api"]) : null;
    
    if (!$authKey || $authKey !== $emailApiKey) {
        http_response_code(401);
        echo json_encode(["status" => "fail", "error" => "Unauthorized"]);
        exit;
    }

    $to = filter_var($_POST["to"], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST["subject"]);
    $message = filter_var($_POST['message'], FILTER_UNSAFE_RAW);
    $headers = "From: noreply@{$host}\r\n";
    $headers .= "Reply-To: noreply@{$host}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $sent = false;
    if ($to && $subject && $message) {
        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully!";
            $sent = true;
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Invalid input.";
    }
    if ($sent) {
        header("Location: ?status=success");
        exit;
    } else {
        header("Location: ?status=fail");
        exit;
    }
}
?>

<?php
$status = $_GET['status'] ?? null;

if ($status === 'success') {
    echo '<p style="color:green;">Email sent successfully!</p>';
} elseif ($status === 'fail') {
    echo '<p style="color:red;">Failed to send email.</p>';
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
        <input type="password" name="api" required><br><br>

        <label for="message">Message:</label><br>
        <textarea name="message" rows="6" cols="40" required></textarea><br><br>

        <input type="submit" value="Send Email">
    </form>
</body>
</html>
