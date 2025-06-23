<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$emailApiKey = getenv('EMAIL_API_KEY');
$host = getenv('HOST');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $headers = getallheaders();
    $authKey = $headers["X-API-KEY"] ?? $headers["x-api-key"] ?? "";

    if ($authKey !== emailApiKey) {
        http_response_code(401);
        echo json_encode(["status" => "fail", "error" => "Unauthorized"]);
        exit;
    }
    
    $data = json_decode(file_get_contents("php://input"), true);

    $to = filter_var($data["to"] ?? "", FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($data["subject"] ?? "");
    $message = htmlspecialchars($data["message"] ?? "");

    $headers = "From: noreply@{$host}\r\n";
    $headers .= "Reply-To: noreply@{$host}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if ($to && $subject && $message) {
        if (mail($to, $subject, $message, $headers)) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "fail", "error" => "mail failed"]);
        }
    } else {
        echo json_encode(["status" => "fail", "error" => "invalid input"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "fail", "error" => "Method not allowed"]);
}