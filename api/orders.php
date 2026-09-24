<?php

require_once "config.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);

    exit;
}

$customerName = trim($input["customer_name"] ?? "");
$phone = trim($input["phone"] ?? "");
$items = $input["items"] ?? [];
$total = (float)($input["total"] ?? 0);

if (
    $customerName === "" ||
    $phone === "" ||
    !is_array($items) ||
    count($items) === 0
) {
    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Name, phone and cart items are required."
    ]);

    exit;
}

$itemsJson = json_encode(
    $items,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

try {

    $stmt = $pdo->prepare("
        INSERT INTO orders
        (
            customer_name,
            phone,
            items_json,
            total,
            status
        )
        VALUES
        (
            :customer_name,
            :phone,
            :items_json,
            :total,
            'Pending'
        )
    ");

    $stmt->execute([
        ":customer_name" => $customerName,
        ":phone" => $phone,
        ":items_json" => $itemsJson,
        ":total" => $total
    ]);

    echo json_encode([
        "success" => true,
        "order_id" => $pdo->lastInsertId(),
        "message" => "Order saved successfully."
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to save order."
    ]);
}
?>