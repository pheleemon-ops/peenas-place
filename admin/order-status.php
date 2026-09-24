<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

$status = $_POST["status"] ?? "";

$allowedStatuses = [
    "Pending",
    "Contacted",
    "Completed",
    "Cancelled"
];

if (
    $id <= 0 ||
    !in_array($status, $allowedStatuses, true)
) {
    header("Location: orders.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ":status" => $status,
        ":id" => $id
    ]);

} catch (PDOException $e) {
    // Return to orders page even if update fails.
}

header("Location: orders.php");
exit;
?>