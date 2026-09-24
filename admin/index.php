<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

$productCount = 0;
$orderCount = 0;
$pendingCount = 0;

try {

    $productCount = (int)$pdo
        ->query("SELECT COUNT(*) FROM products")
        ->fetchColumn();

    $orderCount = (int)$pdo
        ->query("SELECT COUNT(*) FROM orders")
        ->fetchColumn();

    $pendingCount = (int)$pdo
        ->query("
            SELECT COUNT(*)
            FROM orders
            WHERE status = 'Pending'
        ")
        ->fetchColumn();

} catch (PDOException $e) {
    // Keep dashboard usable even if statistics fail.
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

    <title>Dashboard | Peena's Place</title>

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

        .main {
            flex: 1;
            padding: 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .topbar h1 {
            font-family: "Playfair Display", serif;
            font-size: 32px;
        }

        .welcome {
            font-size: 13px;
            color: #81786f;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fffdf9;
            border: 1px solid #dfd7cc;
            padding: 25px;
        }

        .stat-card p {
            color: #827970;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .stat-card strong {
            font-family: "Playfair Display", serif;
            font-size: 34px;
        }

        .panel {
            background: #fffdf9;
            border: 1px solid #dfd7cc;
            padding: 28px;
        }

        .panel h2 {
            font-family: "Playfair Display", serif;
            font-size: 23px;
            margin-bottom: 10px;
        }

        .panel p {
            color: #81786f;
            line-height: 1.7;
            font-size: 14px;
        }

        @media (max-width: 800px) {

            .sidebar {
                width: 200px;
            }

            .main {
                padding: 25px;
            }

            .stats {
                grid-template-columns: 1fr;
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
                gap: 5px;
                overflow-x: auto;
            }

            .nav a {
                white-space: nowrap;
            }

            .logout {
                margin-top: 15px;
            }
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

            <a href="index.php" class="active">
                Dashboard
            </a>

            <a href="products.php">
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

                <h1>Dashboard</h1>

                <p class="welcome">
                    Welcome back,
                    <?= htmlspecialchars($_SESSION["admin_name"]) ?>
                </p>

            </div>

        </div>

        <section class="stats">

            <div class="stat-card">

                <p>Total Products</p>

                <strong>
                    <?= $productCount ?>
                </strong>

            </div>

            <div class="stat-card">

                <p>Total Orders</p>

                <strong>
                    <?= $orderCount ?>
                </strong>

            </div>

            <div class="stat-card">

                <p>Pending Orders</p>

                <strong>
                    <?= $pendingCount ?>
                </strong>

            </div>

        </section>

        <section class="panel">

            <h2>
                Store Management
            </h2>

            <p>
                Your Peena's Place administration system is now connected
                to the store database. Product management, order tracking,
                customer records and store settings will be managed from
                this portal.
            </p>

        </section>

    </main>

</div>

</body>

</html>