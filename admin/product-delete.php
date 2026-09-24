<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

try {

    /*
    |--------------------------------------------------------------------------
    | Get product first
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT image
        FROM products
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $product = $stmt->fetch();

    if (!$product) {
        header("Location: products.php");
        exit;
    }

    $image = $product["image"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Delete database record
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM products
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Delete associated local image
    |--------------------------------------------------------------------------
    |
    | Only delete files inside uploads/products.
    | External URLs are left untouched.
    |
    */

    if (
        $image !== "" &&
        strpos($image, "uploads/products/") === 0 &&
        !preg_match("#^https?://#i", $image)
    ) {

        $uploadRoot = realpath(
            dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            "uploads" .
            DIRECTORY_SEPARATOR .
            "products"
        );

        $imagePath =
            dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            str_replace(
                "/",
                DIRECTORY_SEPARATOR,
                ltrim($image, "/")
            );

        $realImagePath =
            file_exists($imagePath)
                ? realpath($imagePath)
                : false;


        /*
        | Make sure the resolved file is actually
        | inside uploads/products.
        */

        if (
            $uploadRoot !== false &&
            $realImagePath !== false &&
            strpos(
                $realImagePath,
                $uploadRoot . DIRECTORY_SEPARATOR
            ) === 0
        ) {

            if (is_file($realImagePath)) {
                unlink($realImagePath);
            }
        }
    }


} catch (PDOException $e) {

    /*
     * Don't expose database errors to the browser.
     */
}

header("Location: products.php");
exit;

?>