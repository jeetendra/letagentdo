<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Get database credentials from environment variables
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

if (!$host || !$db || !$user || !$pass) {
    http_response_code(500);
    echo json_encode(["status" => "fail", "error" => "Missing database configuration"]);
    exit;
}

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "fail", "error" => "DB connection failed"]);
    exit;
}

// Read POST JSON data
$data = json_decode(file_get_contents("php://input"), true);

$from = $data["from"] ?? "";
$subject = $data["subject"] ?? "";
$body = $data["body"] ?? "";

$from = htmlspecialchars(strip_tags($from), ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars(strip_tags($subject), ENT_QUOTES, 'UTF-8');
$body = htmlspecialchars(strip_tags($body), ENT_QUOTES, 'UTF-8');

// Validate
if (empty($from) || empty($subject) || empty($body)) {
    http_response_code(400);
    echo json_encode(["status" => "fail", "error" => "Missing data"]);
    $mysqli->close();
    exit;
}

// Prepare & insert
$stmt = $mysqli->prepare("INSERT INTO emails (sender, subject, body) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $from, $subject, $body);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "id" => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "fail", "error" => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
