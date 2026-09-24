<?php

require_once "../api/config.php";

$name = "Peena's Place Admin";
$email = "admin@peenasplace.com";
$password = "PeenaAdmin2026!";

$hash = password_hash($password, PASSWORD_DEFAULT);

try {

    $stmt = $pdo->prepare("
        INSERT INTO admins
        (name, email, password_hash)
        VALUES
        (:name, :email, :password_hash)
    ");

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":password_hash" => $hash
    ]);

    echo "<h2>Admin account created successfully.</h2>";
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    echo "<p>Password: " . htmlspecialchars($password) . "</p>";

} catch (PDOException $e) {

    echo "<h2>Could not create admin account.</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>