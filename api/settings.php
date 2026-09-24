<?php

require_once "config.php";

header("Content-Type: application/json");

try {

    $stmt = $pdo->query("
        SELECT setting_key, setting_value
        FROM site_settings
    ");

    $settings = [];

    foreach ($stmt->fetchAll() as $row) {
        $settings[$row["setting_key"]] = $row["setting_value"];
    }

    echo json_encode([
        "success" => true,
        "settings" => $settings
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load site settings."
    ]);
}
?>