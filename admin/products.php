<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            category,
            price,
            image,
            active,
            created_at
        FROM products
        ORDER BY created_at DESC
    ");

    $products = $stmt->fetchAll();

} catch (PDOException $e) {

    $products = [];

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

<title>Products | Peena's Place Admin</title>

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap"
    rel="stylesheet"
>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    background: #f6f2ec;
    color: #29231e;
    font-family: "DM Sans", sans-serif;
}

.layout {
    min-height: 100vh;
    display: flex;
}

.sidebar {
    width: 250px;
    background: #29231e;
    color: white;
    padding: 30px 20px;
}

.logo {
    font-family: "Playfair Display", serif;
    font-size: 25px;
    margin-bottom: 6px;
}

.admin-label {
    font-size: 11px;
    color: #bdb3a8;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 40px;
}

.nav a {
    display: block;
    padding: 13px 14px;
    margin-bottom: 6px;
    color: #d9d0c7;
    text-decoration: none;
    font-size: 14px;
}

.nav a:hover,
.nav a.active {
    background: #40372f;
    color: white;
}

.logout {
    margin-top: 40px;
    border-top: 1px solid #494038;
    padding-top: 20px;
}

.logout a {
    color: #d9d0c7;
    text-decoration: none;
}

.main {
    flex: 1;
    padding: 35px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.topbar h1 {
    font-family: "Playfair Display", serif;
    font-size: 32px;
}

.topbar p {
    color: #81786f;
    font-size: 14px;
    margin-top: 5px;
}

.add-button {
    display: inline-block;
    background: #29231e;
    color: white;
    padding: 13px 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.table-wrapper {
    background: #fffdf9;
    border: 1px solid #dfd7cc;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
}

th,
td {
    padding: 16px;
    border-bottom: 1px solid #e7e0d7;
    text-align: left;
    font-size: 13px;
}

th {
    background: #f4efe7;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: #746b62;
}

.product-image {
    width: 58px;
    height: 58px;
    object-fit: cover;
    display: block;
}

.product-name {
    font-weight: 600;
}

.price {
    font-weight: 600;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 600;
}

.status.active {
    background: #e5f1e7;
    color: #3f7047;
}

.status.inactive {
    background: #eee8e3;
    color: #756b63;
}

.actions a {
    color: #80613b;
    text-decoration: none;
    margin-right: 12px;
}

.empty {
    padding: 50px;
    text-align: center;
    color: #81786f;
}

@media (max-width: 800px) {

    .sidebar {
        width: 200px;
    }

    .main {
        padding: 25px;
    }

}

@media (max-width: 600px) {

    .layout {
        display: block;
    }

    .sidebar {
        width: 100%;
    }

    .nav {
        display: flex;
        overflow-x: auto;
    }

    .nav a {
        white-space: nowrap;
    }

    .topbar {
        display: block;
    }

    .add-button {
        margin-top: 20px;
    }

}

</style>

</head>

<body>

<div class="layout">

<aside class="sidebar">

    <div class="logo">
        Peena's Place
    </div>

    <div class="admin-label">
        Admin Portal
    </div>

    <nav class="nav">

        <a href="index.php">
            Dashboard
        </a>

        <a href="products.php" class="active">
            Products
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="customers.php">
            Customers
        </a>

        <a href="settings.php">
            Settings
        </a>

    </nav>

    <div class="logout">

        <a href="logout.php">
            Logout
        </a>

    </div>

</aside>


<main class="main">

    <div class="topbar">

        <div>

            <h1>Products</h1>

            <p>
                Manage everything displayed in your marketplace.
            </p>

        </div>

        <a
            href="product-add.php"
            class="add-button"
        >
            + Add Product
        </a>

    </div>


    <div class="table-wrapper">

        <?php if (count($products) > 0): ?>

        <table>

            <thead>

                <tr>

                    <th>Image</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($products as $product): ?>

                <tr>

                    <td>

                        <?php if (!empty($product["image"])): ?>

                            <img
                                src="<?= htmlspecialchars($product["image"]) ?>"
                                class="product-image"
                                alt="<?= htmlspecialchars($product["name"]) ?>"
                            >

                        <?php else: ?>

                            <div class="product-image"></div>

                        <?php endif; ?>

                    </td>


                    <td>

                        <div class="product-name">

                            <?= htmlspecialchars($product["name"]) ?>

                        </div>

                    </td>


                    <td>

                        <?= htmlspecialchars($product["category"]) ?>

                    </td>


                    <td class="price">

                        ₦<?= number_format(
                            (float)$product["price"],
                            2
                        ) ?>

                    </td>


                    <td>

                        <?php if ($product["active"]): ?>

                            <span class="status active">
                                Active
                            </span>

                        <?php else: ?>

                            <span class="status inactive">
                                Inactive
                            </span>

                        <?php endif; ?>

                    </td>


                    <td class="actions">

                        <a
                            href="product-edit.php?id=<?= $product["id"] ?>"
                        >
                            Edit
                        </a>

                        <form
    method="POST"
    action="product-delete.php"
    style="display:inline;"
    onsubmit="return confirm('Delete this product permanently?');"
>

    <input
        type="hidden"
        name="id"
        value="<?= (int)$product["id"] ?>"
    >

    <button
        type="submit"
        style="
            border:none;
            background:none;
            color:#a94a42;
            cursor:pointer;
            font:inherit;
        "
    >
        Delete
    </button>

</form>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php else: ?>

            <div class="empty">

                No products found.

            </div>

        <?php endif; ?>

    </div>

</main>

</div>

</body>

</html>