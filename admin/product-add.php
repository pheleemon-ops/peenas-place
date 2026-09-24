<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

/*product categories */
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $sku = trim($_POST["sku"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $salePriceInput = trim($_POST["sale_price"] ?? "");
    $salePrice = $salePriceInput === "" ? null : (float)$salePriceInput;
    $stockStatus = $_POST["stock_status"] ?? "In Stock";
    $description = trim($_POST["description"] ?? "");
    $image = trim($_POST["image"] ?? "");
    $featured = isset($_POST["featured"]) ? 1 : 0;

    

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

        try {

            /*
             * Generate SKU automatically if the admin leaves it empty.
             */
            if ($sku === "") {
                $sku = "PP-" . strtoupper(substr(md5(uniqid()), 0, 8));
            }

            /*
             * Make sure the SKU is unique.
             */
            $skuCheck = $pdo->prepare("
                SELECT id
                FROM products
                WHERE sku = :sku
                LIMIT 1
            ");

            $skuCheck->execute([
                ":sku" => $sku
            ]);

            if ($skuCheck->fetch()) {

                $message = "That SKU already exists. Please use another SKU.";
                $messageType = "error";

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO products
                    (
                        sku,
                        name,
                        category,
                        price,
                        stock_status,
                        sale_price,
                        image,
                        description,
                        active,
                        featured
                    )
                    VALUES
                    (
                        :sku,
                        :name,
                        :category,
                        :price,
                        :stock_status,
                        :sale_price,
                        :image,
                        :description,
                        1,
                        :featured
                    )
                ");

                $stmt->execute([
                    ":sku" => $sku,
                    ":name" => $name,
                    ":category" => $category,
                    ":price" => $price,
                    ":stock_status" => $stockStatus,
                    ":sale_price" => $salePrice,
                    ":image" => $image,
                    ":description" => $description,
                    ":featured" => $featured
                ]);

                header("Location: products.php?added=1");
                exit;
            }

        } catch (PDOException $e) {

            $message = "Unable to add product. Please check the database.";
            $messageType = "error";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Product | Peena's Place</title>

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

        .upload-status {
            margin-top: 10px;
            font-size: 14px;
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

        <h1>Add Product</h1>

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

        <form method="POST" id="productForm">

            <div class="grid">

                <div>
                    <label>Product Name</label>

                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="e.g. Aurelia Lounge Chair"
                        value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
                    >
                </div>

                <div>
                    <label>SKU</label>

                    <input
                        type="text"
                        name="sku"
                        placeholder="Leave empty to generate automatically"
                        value="<?= htmlspecialchars($_POST["sku"] ?? "") ?>"
                    >

                    <span class="hint">
                        Example: PP-CHAIR-001
                    </span>
                </div>

                <div>
                    <label>Category</label>

                    <select name="category" required>

                        <option value="">Select category</option>

                        <?php foreach ($allowedCategories as $cat): ?>

                            <option
                                value="<?= htmlspecialchars($cat) ?>"
                                <?= (($_POST["category"] ?? "") === $cat) ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($cat) ?>
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
                                <?= (($_POST["stock_status"] ?? "In Stock") === $status) ? "selected" : "" ?>
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
                        placeholder="285000"
                        value="<?= htmlspecialchars($_POST["price"] ?? "") ?>"
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
                        value="<?= htmlspecialchars($_POST["sale_price"] ?? "") ?>"
                    >

                    <span class="hint">
                        Must be lower than the regular price.
                    </span>
                </div>

                <div class="full">

                    <label>Product Image</label>

                    <div class="upload-box">

                        <input
                            type="file"
                            id="imageInput"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <input
                            type="hidden"
                            name="image"
                            id="imagePath"
                            value="<?= htmlspecialchars($_POST["image"] ?? "") ?>"
                        >

                        <div class="upload-status" id="uploadStatus">
                            Select an image to upload.
                        </div>

                        <div class="preview">
                            <img id="imagePreview" alt="Product preview">
                        </div>

                    </div>

                </div>

                <div class="full">

                    <label>Description</label>

                    <textarea
                        name="description"
                        placeholder="Describe the product..."
                    ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

                </div>

                <div class="full">

                    <label class="checkbox-row">

                        <input
                            type="checkbox"
                            name="featured"
                            value="1"
                            <?= isset($_POST["featured"]) ? "checked" : "" ?>
                        >

                        <span>
                            Feature this product on the homepage
                        </span>

                    </label>

                </div>

            </div>

            <button
                type="submit"
                class="btn"
                id="saveButton"
            >
                Save Product
            </button>

        </form>

    </div>

</div>

<script>

const imageInput = document.getElementById("imageInput");
const imagePath = document.getElementById("imagePath");
const imagePreview = document.getElementById("imagePreview");
const uploadStatus = document.getElementById("uploadStatus");

imageInput.addEventListener("change", async function () {

    const file = this.files[0];

    if (!file) {
        return;
    }

    if (file.size > 5 * 1024 * 1024) {

        uploadStatus.textContent = "Image is too large. Maximum size is 5 MB.";
        imageInput.value = "";
        return;
    }

    imagePreview.src = URL.createObjectURL(file);
    imagePreview.style.display = "block";

    uploadStatus.textContent = "Uploading image...";

    const formData = new FormData();

    formData.append("image", file);

    try {

        const response = await fetch("../api/upload-product-image.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (!data.success) {

            uploadStatus.textContent =
                data.message || "Image upload failed.";

            imagePath.value = "";
            return;
        }

        imagePath.value = data.path;

        uploadStatus.textContent = "✓ Image uploaded successfully.";

    } catch (error) {

        console.error(error);

        uploadStatus.textContent =
            "Image upload failed. Please try again.";

        imagePath.value = "";
    }

});

</script>

</body>
</html>