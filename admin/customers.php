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
            customer_name,
            phone,
            COUNT(*) AS order_count,
            SUM(total) AS total_spent,
            MAX(created_at) AS last_order
        FROM orders
        GROUP BY customer_name, phone
        ORDER BY last_order DESC
    ");

    $customers = $stmt->fetchAll();

} catch (PDOException $e) {

    $customers = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Customers | Peena's Place Admin</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">

<style>

:root {
    --cream: #f7f3ec;
    --white: #ffffff;
    --dark: #25211d;
    --muted: #77706a;
    --border: #e5ded4;
    --gold: #a78355;
    --sidebar: #201c18;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "DM Sans", sans-serif;
    background: var(--cream);
    color: var(--dark);
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: var(--sidebar);
    color: white;
    padding: 30px 20px;
}

.logo {
    font-family: "Playfair Display", serif;
    font-size: 25px;
    margin-bottom: 40px;
}

.logo span {
    display: block;
    font-family: "DM Sans", sans-serif;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #aaa098;
    margin-top: 5px;
}

.nav a {
    display: block;
    color: #cfc7be;
    text-decoration: none;
    padding: 13px 14px;
    border-radius: 8px;
    margin-bottom: 5px;
    font-size: 14px;
}

.nav a:hover,
.nav a.active {
    background: #302a24;
    color: white;
}

.logout {
    position: absolute;
    bottom: 25px;
    left: 20px;
    right: 20px;
}

.logout a {
    display: block;
    text-align: center;
    padding: 11px;
    border: 1px solid #4a433d;
    border-radius: 8px;
    color: #d9d0c7;
    text-decoration: none;
    font-size: 13px;
}

.main {
    margin-left: 250px;
    padding: 40px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.title h1 {
    margin: 0;
    font-family: "Playfair Display", serif;
    font-size: 34px;
}

.title p {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
}

th {
    text-align: left;
    padding: 16px 18px;
    background: #faf8f4;
    border-bottom: 1px solid var(--border);
    color: #766e66;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

td {
    padding: 18px;
    border-bottom: 1px solid #eee8e0;
    font-size: 14px;
}

tr:last-child td {
    border-bottom: none;
}

.customer-name {
    font-weight: 600;
}

.phone {
    color: var(--muted);
    margin-top: 4px;
    font-size: 13px;
}

.orders {
    font-weight: 600;
}

.amount {
    font-weight: 600;
}

.date {
    color: var(--muted);
    font-size: 13px;
}

.whatsapp {
    display: inline-block;
    text-decoration: none;
    color: #fff;
    background: #3f7656;
    padding: 8px 12px;
    border-radius: 7px;
    font-size: 12px;
}

.empty {
    padding: 60px 20px;
    text-align: center;
    color: var(--muted);
}

@media (max-width: 800px) {

    .sidebar {
        position: static;
        width: 100%;
        height: auto;
        padding: 20px;
    }

    .logo {
        margin-bottom: 20px;
    }

    .nav {
        display: flex;
        gap: 5px;
        overflow-x: auto;
    }

    .nav a {
        white-space: nowrap;
    }

    .logout {
        position: static;
        margin-top: 15px;
    }

    .main {
        margin-left: 0;
        padding: 25px 15px;
    }

    .topbar {
        display: block;
    }

    .title h1 {
        font-size: 28px;
    }
}

</style>

</head>

<body>

<aside class="sidebar">

    <div class="logo">
        Peena's Place
        <span>Admin Application</span>
    </div>

    <nav class="nav">

        <a href="index.php">
            Dashboard
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="customers.php" class="active">
            Customers
        </a>

        <a href="#">
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

        <div class="title">

            <h1>Customers</h1>

            <p>
                Customers who have placed orders through Peena's Place.
            </p>

        </div>

    </div>


    <div class="card">

        <?php if (count($customers) > 0): ?>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>Customer</th>

                            <th>Orders</th>

                            <th>Total Spent</th>

                            <th>Last Order</th>

                            <th>Contact</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($customers as $customer): ?>

                        <tr>

                            <td>

                                <div class="customer-name">
                                    <?= htmlspecialchars($customer["customer_name"]) ?>
                                </div>

                                <div class="phone">
                                    <?= htmlspecialchars($customer["phone"]) ?>
                                </div>

                            </td>


                            <td>

                                <span class="orders">
                                    <?= (int)$customer["order_count"] ?>
                                </span>

                            </td>


                            <td>

                                <span class="amount">
                                    ₦<?= number_format((float)$customer["total_spent"], 2) ?>
                                </span>

                            </td>


                            <td>

                                <span class="date">

                                    <?= date(
                                        "M j, Y",
                                        strtotime($customer["last_order"])
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                $phone = preg_replace(
                                    "/[^0-9]/",
                                    "",
                                    $customer["phone"]
                                );

                                if (str_starts_with($phone, "0")) {
                                    $phone = "234" . substr($phone, 1);
                                }

                                ?>

                                <a
                                    class="whatsapp"
                                    href="https://wa.me/<?= htmlspecialchars($phone) ?>"
                                    target="_blank"
                                >
                                    WhatsApp
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">

                <h3>No customers yet</h3>

                <p>
                    Customers will appear here automatically after they place an order.
                </p>

            </div>

        <?php endif; ?>

    </div>

</main>

</body>
</html>