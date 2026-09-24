<?php

session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$error = $_SESSION["login_error"] ?? "";
unset($_SESSION["login_error"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Peena's Place</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4efe7;
            font-family: "DM Sans", sans-serif;
            color: #25211d;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand h1 {
            font-family: "Playfair Display", serif;
            font-size: 36px;
            font-weight: 600;
        }

        .brand p {
            margin-top: 7px;
            color: #80776e;
            font-size: 14px;
        }

        .login-card {
            background: #fffdf9;
            border: 1px solid #ded6cb;
            padding: 38px;
            box-shadow: 0 20px 60px rgba(50, 40, 30, .08);
        }

        .login-card h2 {
            font-family: "Playfair Display", serif;
            font-size: 27px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #80776e;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 19px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d8d0c6;
            background: #fff;
            font: inherit;
            outline: none;
            transition: border-color .2s ease;
        }

        input:focus {
            border-color: #a17b43;
        }

        button {
            width: 100%;
            border: 0;
            padding: 15px;
            background: #29231e;
            color: white;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: transform .2s ease, opacity .2s ease;
        }

        button:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .error {
            background: #f8e5e2;
            color: #9a4037;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .back a {
            color: #80613b;
            text-decoration: none;
            font-size: 13px;
        }

    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="brand">
        <h1>Peena's Place</h1>
        <p>Administration Portal</p>
    </div>

    <div class="login-card">

        <h2>Welcome back</h2>

        <p class="subtitle">
            Sign in to manage your store.
        </p>

        <?php if ($error): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login-process.php">

            <div class="form-group">

                <label for="email">
                    Email address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="admin@peenasplace.com"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit">
                Sign in
            </button>

        </form>

        <div class="back">
            <a href="../index.html">
                ← Back to Peena's Place
            </a>
        </div>

    </div>

</div>

</body>
</html>