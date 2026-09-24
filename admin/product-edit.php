<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$allowedCategories = [
    "Furniture",
    "Wall Décor",
    "Lighting",
    "Soft Furnishing",
    "Accessories",
    "Custom Design Services"
];

$allowedStockStatuses = [
    "In Stock",
    "Low Stock",
    "Out of Stock"
];

$message = "";
$messageType = "";

/*
 * Load product
 */
$stmt = $pdo->prepare("
    SELECT *
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

/*
 * Update product
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $sku = trim($_POST["sku"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = (float)($_POST["price"] ?? 0);

    $salePriceInput = trim($_POST["sale_price"] ?? "");
    $salePrice = $salePriceInput === ""
        ? null
        : (float)$salePriceInput;

    $stockStatus = $_POST["stock_status"] ?? "In Stock";

    $description = trim($_POST["description"] ?? "");

    $active = isset($_POST["active"]) ? 1 : 0;
    $featured = isset($_POST["featured"]) ? 1 : 0;

    $newImagePath = $product["image"];
    $uploadedNewImage = false;

    /*
     * Basic validation
     */
    if ($name === "" || $category === "" || $price <= 0) {

        $message = "Product name, category and valid price are required.";
        $messageType = "error";

    } elseif (!in_array($category, $allowedCategories, true)) {

        $message = "Invalid product category.";
        $messageType = "error";

    } elseif (!in_array($stockStatus, $allowedStockStatuses, true)) {

        $message = "Invalid stock status.";
        $messageType = "error";

    } elseif ($salePrice !== null && ($salePrice <= 0 || $salePrice >= $price)) {

        $message = "Sale price must be greater than 0 and lower than the regular price.";
        $messageType = "error";

    } else {

        /*
         * Check SKU uniqueness.
         */
        if ($sku === "") {

            $sku = $product["sku"];

            /*
             * For old products that somehow have no SKU,
             * generate one automatically.
             */
            if (!$sku) {
                $sku = "PP-" . strtoupper(substr(md5(uniqid()), 0, 8));
            }

        }

        $skuCheck = $pdo->prepare("
            SELECT id
            FROM products
            WHERE sku = :sku
            AND id != :id
            LIMIT 1
        ");

        $skuCheck->execute([
            ":sku" => $sku,
            ":id" => $id
        ]);

        if ($skuCheck->fetch()) {

            $message = "That SKU is already being used by another product.";
            $messageType = "error";

        } else {

            /*
             * Handle replacement image.
             */
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                    $message = "Image upload failed.";
                    $messageType = "error";

                } elseif ($_FILES["image"]["size"] > 5 * 1024 * 1024) {

                    $message = "Image is too large. Maximum size is 5 MB.";
                    $messageType = "error";

                } else {

                    $tmpFile = $_FILES["image"]["tmp_name"];

                    /*
                     * Verify that it is actually an image.
                     */
                    $imageInfo = @getimagesize($tmpFile);

                    if ($imageInfo === false) {

                        $message = "The uploaded file is not a valid image.";
                        $messageType = "error";

                    } else {

                        /*
                         * Verify MIME type.
                         */
                        $finfo = new finfo(FILEINFO_MIME_TYPE);

                        $mimeType = $finfo->file($tmpFile);

                        $allowedMimeTypes = [
                            "image/jpeg" => "jpg",
                            "image/png" => "png",
                            "image/webp" => "webp"
                        ];

                        if (!isset($allowedMimeTypes[$mimeType])) {

                            $message = "Only JPG, PNG and WebP images are allowed.";
                            $messageType = "error";

                        } else {

                            $uploadDirectory = "../uploads/products/";

                            if (!is_dir($uploadDirectory)) {
                                mkdir($uploadDirectory, 0755, true);
                            }

                            $extension = $allowedMimeTypes[$mimeType];

                            $filename =
                                bin2hex(random_bytes(16))
                                . "."
                                . $extension;

                            $destination =
                                $uploadDirectory . $filename;

                            if (!move_uploaded_file($tmpFile, $destination)) {

                                $message = "Unable to save the new image.";
                                $messageType = "error";

                            } else {

                                $newImagePath =
                                    "/peenas-place/uploads/products/"
                                    . $filename;

                                $uploadedNewImage = true;
                            }
                        }
                    }
                }
            }

            /*
             * Only continue if there was no image error.
             */
            if ($message === "") {

                try {

                    $stmt = $pdo->prepare("
                        UPDATE products
                        SET
                            sku = :sku,
                            name = :name,
                            category = :category,
                            price = :price,
                            stock_status = :stock_status,
                            sale_price = :sale_price,
                            image = :image,
                            description = :description,
                            active = :active,
                            featured = :featured
                        WHERE id = :id
                    ");

                    $stmt->execute([
                        ":sku" => $sku,
                        ":name" => $name,
                        ":category" => $category,
                        ":price" => $price,
                        ":stock_status" => $stockStatus,
                        ":sale_price" => $salePrice,
                        ":image" => $newImagePath,
                        ":description" => $description,
                        ":active" => $active,
                        ":featured" => $featured,
                        ":id" => $id
                    ]);

                    /*
                     * Delete the old image only after
                     * the database update succeeds.
                     */
                    if (
                        $uploadedNewImage &&
                        !empty($product["image"])
                    ) {

                        $oldImage = $product["image"];

                        /*
                         * Only delete local product uploads.
                         * Never delete external URLs.
                         */
                        if (
                            strpos($oldImage, "/peenas-place/uploads/products/") === 0
                        ) {

                            $oldFilename = basename($oldImage);

                            $oldFile =
                                realpath(
                                    "../uploads/products/" . $oldFilename
                                );

                            $uploadFolder =
                                realpath("../uploads/products");

                            if (
                                $oldFile &&
                                $uploadFolder &&
                                strpos($oldFile, $uploadFolder . DIRECTORY_SEPARATOR) === 0 &&
                                is_file($oldFile)
                            ) {
                                unlink($oldFile);
                            }
                        }
                    }

                    header("Location: products.php?updated=1");
                    exit;

                } catch (PDOException $e) {

                    /*
                     * If the database update failed,
                     * remove the newly uploaded image.
                     */
                    if ($uploadedNewImage && !empty($newImagePath)) {

                        $newFilename = basename($newImagePath);

                        $newFile =
                            realpath(
                                "../uploads/products/" . $newFilename
                            );

                        $uploadFolder =
                            realpath("../uploads/products");

                        if (
                            $newFile &&
                            $uploadFolder &&
                            strpos($newFile, $uploadFolder . DIRECTORY_SEPARATOR) === 0 &&
                            is_file($newFile)
                        ) {
                            unlink($newFile);
                        }
                    }

                    $message = "Unable to update product. Please check the database.";
                    $messageType = "error";
                }
            }
        }
    }

    /*
     * Keep form values after validation error.
     */
    $product["name"] = $name;
    $product["sku"] = $sku;
    $product["category"] = $category;
    $product["price"] = $price;
    $product["sale_price"] = $salePrice;
    $product["stock_status"] = $stockStatus;
    $product["description"] = $description;
    $product["active"] = $active;
    $product["featured"] = $featured;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product | Peena's Place</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f2ec;
            color: #2d2925;
        }

        .page {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0;
            font-family: Georgia, serif;
            font-size: 32px;
        }

        .back {
            text-decoration: none;
            color: #7b5b3a;
            font-weight: bold;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 35px rgba(0,0,0,.06);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #d8d0c7;
            border-radius: 9px;
            font-size: 15px;
            background: #fff;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: #777;
            font-size: 13px;
        }

        .current-image {
            margin-bottom: 20px;
        }

        .current-image img {
            width: 220px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            display: block;
        }

        .upload-box {
            border: 2px dashed #c9b9a7;
            border-radius: 12px;
            padding: 20px;
            background: #faf8f5;
        }

        .preview {
            margin-top: 15px;
        }

        .preview img {
            max-width: 240px;
            max-height: 220px;
            border-radius: 10px;
            display: none;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
        }

        .checkbox-row input {
            width: auto;
        }

        .btn {
            margin-top: 25px;
            width: 100%;
            border: none;
            padding: 15px;
            border-radius: 10px;
            background: #2d2925;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background: #7b5b3a;
        }

        .message {
            padding: 13px;
            border-radius: 9px;
            margin-bottom: 20px;
        }

        .error {
            background: #f9e5e3;
            color: #8d3028;
        }

        .upload-status {
            margin-top: 10px;
            color: #777;
            font-size: 14px;
        }

        @media (max-width: 700px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

            .top {
                align-items: flex-start;
                flex-direction: column;
            }

            .card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="top">

        <h1>Edit Product</h1>

        <a href="products.php" class="back">
            ← Back to Products
        </a>

    </div>

    <?php if ($message): ?>

        <div class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <div class="card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="grid">

                <div>

                    <label>Product Name</label>

                    <input
                        type="text"
                        name="name"
                        required
                        value="<?= htmlspecialchars($product["name"]) ?>"
                    >

                </div>

                <div>

                    <label>SKU</label>

                    <input
                        type="text"
                        name="sku"
                        value="<?= htmlspecialchars($product["sku"] ?? "") ?>"
                    >

                    <span class="hint">
                        Must be unique.
                    </span>

                </div>

                <div>

                    <label>Category</label>

                    <select name="category" required>

                        <?php foreach ($allowedCategories as $category): ?>

                            <option
                                value="<?= htmlspecialchars($category) ?>"
                                <?= $product["category"] === $category ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($category) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div>

                    <label>Stock Status</label>

                    <select name="stock_status">

                        <?php foreach ($allowedStockStatuses as $status): ?>

                            <option
                                value="<?= htmlspecialchars($status) ?>"
                                <?= ($product["stock_status"] ?? "In Stock") === $status ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($status) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div>

                    <label>Regular Price (₦)</label>

                    <input
                        type="number"
                        name="price"
                        min="1"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars($product["price"]) ?>"
                    >

                </div>

                <div>

                    <label>Sale Price (₦)</label>

                    <input
                        type="number"
                        name="sale_price"
                        min="1"
                        step="0.01"
                        placeholder="Optional"
                        value="<?= htmlspecialchars($product["sale_price"] ?? "") ?>"
                    >

                    <span class="hint">
                        Leave empty if there is no discount.
                    </span>

                </div>

                <div class="full">

                    <label>Current Image</label>

                    <div class="current-image">

                        <?php if (!empty($product["image"])): ?>

                            <img
                                src="<?= htmlspecialchars($product["image"]) ?>"
                                alt="<?= htmlspecialchars($product["name"]) ?>"
                            >

                        <?php else: ?>

                            <p>No product image uploaded.</p>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="full">

                    <label>Replace Image</label>

                    <div class="upload-box">

                        <input
                            type="file"
                            name="image"
                            id="imageInput"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div
                            class="upload-status"
                            id="uploadStatus"
                        >
                            Optional. Leave empty to keep the current image.
                        </div>

                        <div class="preview">

                            <img
                                id="imagePreview"
                                alt="New image preview"
                            >

                        </div>

                    </div>

                </div>

                <div class="full">

                    <label>Description</label>

                    <textarea
                        name="description"
                    ><?= htmlspecialchars($product["description"] ?? "") ?></textarea>

                </div>

                <div class="full">

                    <label class="checkbox-row">

                        <input
                            type="checkbox"
                            name="featured"
                            value="1"
                            <?= !empty($product["featured"]) ? "checked" : "" ?>
                        >

                        <span>
                            Featured product
                        </span>

                    </label>

                </div>

                <div class="full">

                    <label class="checkbox-row">

                        <input
                            type="checkbox"
                            name="active"
                            value="1"
                            <?= !empty($product["active"]) ? "checked" : "" ?>
                        >

                        <span>
                            Product is active and visible on the website
                        </span>

                    </label>

                </div>

            </div>

            <button
                type="submit"
                class="btn"
            >
                Save Changes
            </button>

        </form>

    </div>

</div>

<script>

const imageInput = document.getElementById("imageInput");
const imagePreview = document.getElementById("imagePreview");
const uploadStatus = document.getElementById("uploadStatus");

imageInput.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) {
        imagePreview.style.display = "none";
        uploadStatus.textContent =
            "Optional. Leave empty to keep the current image.";
        return;
    }

    if (file.size > 5 * 1024 * 1024) {

        uploadStatus.textContent =
            "Image is too large. Maximum size is 5 MB.";

        imageInput.value = "";
        imagePreview.style.display = "none";

        return;
    }

    imagePreview.src = URL.createObjectURL(file);
    imagePreview.style.display = "block";

    uploadStatus.textContent =
        "New image selected. It will replace the current image when you save.";

});

</script>

</body>

</html>