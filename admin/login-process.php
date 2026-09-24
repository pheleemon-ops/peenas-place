<?php

session_start();

require_once "../api/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {

    $_SESSION["login_error"] = "Please enter your email and password.";

    header("Location: login.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            email,
            password_hash
        FROM admins
        WHERE email = :email
        LIMIT 1
    ");

    $stmt->execute([
        ":email" => $email
    ]);

    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin["password_hash"])) {

        $_SESSION["login_error"] = "Invalid email or password.";

        header("Location: login.php");
        exit;
    }

    session_regenerate_id(true);

    $_SESSION["admin_id"] = $admin["id"];
    $_SESSION["admin_name"] = $admin["name"];
    $_SESSION["admin_email"] = $admin["email"];

    header("Location: index.php");
    exit;

} catch (PDOException $e) {

    $_SESSION["login_error"] = "Unable to process login.";

    header("Location: login.php");
    exit;
}