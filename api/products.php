<?php

require_once "config.php";

header("Content-Type: application/json");

try {

    $stmt = $pdo->query("
        SELECT
            id,
            sku,
            name,
            category,
            price,
            stock_status,
            sale_price,
            image,
            description,
            active,
            featured,
            created_at
        FROM products
        WHERE active = 1
        ORDER BY created_at DESC
    ");

    $products = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "products" => $products
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load products."
    ]);
}

?>