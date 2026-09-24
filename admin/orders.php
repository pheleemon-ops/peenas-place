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
            customer_name,
            phone,
            items_json,
            total,
            status,
            created_at
        FROM orders
        ORDER BY created_at DESC
    ");

    $orders = $stmt->fetchAll();

} catch (PDOException $e) {

    $orders = [];

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

<title>Orders | Peena's Place Admin</title>

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

.table-wrapper {
    background: #fffdf9;
    border: 1px solid #dfd7cc;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th,
td {
    padding: 16px;
    border-bottom: 1px solid #e7e0d7;
    text-align: left;
    font-size: 13px;
    vertical-align: top;
}

th {
    background: #f4efe7;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: #746b62;
}

.customer-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.phone {
    color: #81786f;
}

.items {
    max-width: 260px;
    line-height: 1.6;
}

.total {
    font-weight: 600;
    white-space: nowrap;
}

.date {
    color: #81786f;
    white-space: nowrap;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
}

.status.pending {
    background: #f5ead5;
    color: #8a642c;
}

.status.contacted {
    background: #e5edf5;
    color: #49657e;
}

.status.completed {
    background: #e5f1e7;
    color: #3f7047;
}

.status.cancelled {
    background: #f8e5e2;
    color: #9a4037;
}

.status-form select {
    padding: 8px;
    border: 1px solid #d8d0c6;
    background: white;
    font: inherit;
    font-size: 12px;
}

.empty {
    padding: 60px;
    text-align: center;
    color: #81786f;
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

    .main {
        padding: 25px 18px;
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

        <a href="products.php">
            Products
        </a>

        <a href="orders.php" class="active">
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

        <h1>
            Orders
        </h1>

        <p>
            Track and manage customer checkout requests.
        </p>

    </div>


    <div class="table-wrapper">

    <?php if (count($orders) > 0): ?>

        <table>

            <thead>

                <tr>

                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($orders as $order): ?>

                <?php

                $items = json_decode(
                    $order["items_json"],
                    true
                );

                ?>

                <tr>

                    <td>

                        <div class="customer-name">

                            <?= htmlspecialchars(
                                $order["customer_name"]
                            ) ?>

                        </div>

                        <div class="phone">

                            <?= htmlspecialchars(
                                $order["phone"]
                            ) ?>

                        </div>

                    </td>


                    <td>

                        <div class="items">

                        <?php if (is_array($items)): ?>

                            <?php foreach ($items as $item): ?>

                                <?= htmlspecialchars(
                                    $item["name"] ?? "Product"
                                ) ?>

                                ×

                                <?= (int)(
                                    $item["quantity"] ?? 1
                                ) ?>

                                <br>

                            <?php endforeach; ?>

                        <?php else: ?>

                            Order details unavailable.

                        <?php endif; ?>

                        </div>

                    </td>


                    <td class="total">

                        ₦<?= number_format(
                            (float)$order["total"],
                            2
                        ) ?>

                    </td>


                    <td class="date">

                        <?= htmlspecialchars(
                            $order["created_at"]
                        ) ?>

                    </td>


                    <td>

                        <form
                            method="POST"
                            action="order-status.php"
                            class="status-form"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $order["id"] ?>"
                            >

                            <select
                                name="status"
                                onchange="this.form.submit()"
                            >

                                <?php

                                $statuses = [
                                    "Pending",
                                    "Contacted",
                                    "Completed",
                                    "Cancelled"
                                ];

                                foreach ($statuses as $status):

                                ?>

                                    <option
                                        value="<?= $status ?>"
                                        <?= $order["status"] === $status
                                            ? "selected"
                                            : "" ?>
                                    >
                                        <?= $status ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <div class="empty">

            No orders have been received yet.

        </div>

    <?php endif; ?>

    </div>

</main>

</div>

</body>

</html>