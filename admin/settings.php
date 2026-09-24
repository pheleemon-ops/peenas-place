<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../api/config.php";

$settings = [];

try {
    $stmt = $pdo->query("
        SELECT setting_key, setting_value
        FROM site_settings
    ");

    foreach ($stmt->fetchAll() as $row) {
        $settings[$row["setting_key"]] = $row["setting_value"];
    }

} catch (PDOException $e) {
    $error = "Unable to load settings.";
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $allowedFields = [
        "business_name",
        "location",
        "whatsapp",
        "email",
        "instagram",
        "instagram_url",
        "about_text",
        "founder_name",
        "founder_bio",
        "opening_hours",
        "maps_url"
    ];

    try {

        $stmt = $pdo->prepare("
            INSERT INTO site_settings
            (setting_key, setting_value)
            VALUES (:key, :value)

            ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value)
        ");

        foreach ($allowedFields as $field) {

            $value = trim($_POST[$field] ?? "");

            $stmt->execute([
                ":key" => $field,
                ":value" => $value
            ]);

            $settings[$field] = $value;
        }

        $message = "Settings saved successfully.";
        $messageType = "success";

    } catch (PDOException $e) {

        $message = "Unable to save settings.";
        $messageType = "error";
    }
}

function setting($key, $settings) {
    return htmlspecialchars(
        $settings[$key] ?? "",
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Settings | Peena's Place Admin</title>

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
    --success: #3f7656;
    --error: #a94a42;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--cream);
    color: var(--dark);
    font-family: "DM Sans", sans-serif;
}

/* SIDEBAR */

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
    margin-top: 5px;
    color: #aaa098;
    font-family: "DM Sans", sans-serif;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.nav a {
    display: block;
    padding: 13px 14px;
    margin-bottom: 5px;
    border-radius: 8px;
    color: #cfc7be;
    text-decoration: none;
    font-size: 14px;
}

.nav a:hover,
.nav a.active {
    background: #302a24;
    color: white;
}

.logout {
    position: absolute;
    left: 20px;
    right: 20px;
    bottom: 25px;
}

.logout a {
    display: block;
    padding: 11px;
    text-align: center;
    border: 1px solid #4a433d;
    border-radius: 8px;
    color: #d9d0c7;
    text-decoration: none;
    font-size: 13px;
}

/* MAIN */

.main {
    margin-left: 250px;
    padding: 40px;
    max-width: 1200px;
}

.topbar {
    margin-bottom: 30px;
}

.topbar h1 {
    margin: 0;
    font-family: "Playfair Display", serif;
    font-size: 34px;
}

.topbar p {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 14px;
}

/* ALERT */

.alert {
    margin-bottom: 25px;
    padding: 14px 16px;
    border-radius: 9px;
    font-size: 14px;
}

.alert.success {
    background: #eaf4ed;
    color: var(--success);
    border: 1px solid #cfe3d4;
}

.alert.error {
    background: #faecea;
    color: var(--error);
    border: 1px solid #efd1ce;
}

/* FORM */

.settings-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 30px;
    margin-bottom: 25px;
}

.section-title {
    margin-bottom: 25px;
}

.section-title h2 {
    margin: 0;
    font-family: "Playfair Display", serif;
    font-size: 22px;
}

.section-title p {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.field {
    display: flex;
    flex-direction: column;
}

.field.full {
    grid-column: 1 / -1;
}

label {
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 600;
}

input,
textarea {
    width: 100%;
    padding: 13px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: #fffdf9;
    color: var(--dark);
    font-family: "DM Sans", sans-serif;
    font-size: 14px;
    outline: none;
}

input:focus,
textarea:focus {
    border-color: var(--gold);
}

textarea {
    min-height: 130px;
    resize: vertical;
}

.help {
    margin-top: 6px;
    color: var(--muted);
    font-size: 11px;
}

.save-area {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}

.save-btn {
    border: none;
    border-radius: 8px;
    padding: 13px 24px;
    background: var(--dark);
    color: white;
    cursor: pointer;
    font-family: "DM Sans", sans-serif;
    font-weight: 600;
    font-size: 14px;
}

.save-btn:hover {
    background: #3a332c;
}

/* MOBILE */

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

    .grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: auto;
    }

    .settings-card {
        padding: 20px;
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

        <a href="customers.php">
            Customers
        </a>

        <a href="settings.php" class="active">
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

        <h1>Settings</h1>

        <p>
            Manage the information displayed across Peena's Place.
        </p>

    </div>


    <?php if ($message !== ""): ?>

        <div class="alert <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <form method="POST">


        <!-- BUSINESS INFORMATION -->

        <div class="settings-card">

            <div class="section-title">

                <h2>Business Information</h2>

                <p>
                    Basic information about the Peena's Place brand.
                </p>

            </div>


            <div class="grid">

                <div class="field">

                    <label>
                        Business Name
                    </label>

                    <input
                        type="text"
                        name="business_name"
                        value="<?= setting("business_name", $settings) ?>"
                    >

                </div>


                <div class="field">

                    <label>
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        value="<?= setting("location", $settings) ?>"
                    >

                </div>


                <div class="field">

                    <label>
                        WhatsApp Number
                    </label>

                    <input
                        type="text"
                        name="whatsapp"
                        value="<?= setting("whatsapp", $settings) ?>"
                    >

                    <span class="help">
                        Example: +234 706 256 9181
                    </span>

                </div>


                <div class="field">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="<?= setting("email", $settings) ?>"
                    >

                </div>


                <div class="field">

                    <label>
                        Instagram Handle
                    </label>

                    <input
                        type="text"
                        name="instagram"
                        value="<?= setting("instagram", $settings) ?>"
                    >

                </div>


                <div class="field">

                    <label>
                        Instagram URL
                    </label>

                    <input
                        type="url"
                        name="instagram_url"
                        value="<?= setting("instagram_url", $settings) ?>"
                        placeholder="https://instagram.com/..."
                    >

                </div>


                <div class="field full">

                    <label>
                        Google Maps URL
                    </label>

                    <input
                        type="url"
                        name="maps_url"
                        value="<?= setting("maps_url", $settings) ?>"
                        placeholder="https://maps.google.com/..."
                    >

                </div>


                <div class="field full">

                    <label>
                        Opening Hours
                    </label>

                    <input
                        type="text"
                        name="opening_hours"
                        value="<?= setting("opening_hours", $settings) ?>"
                    >

                </div>

            </div>

        </div>


        <!-- ABOUT -->

        <div class="settings-card">

            <div class="section-title">

                <h2>About Peena's Place</h2>

                <p>
                    This content will be used on the public website.
                </p>

            </div>


            <div class="field">

                <label>
                    About Description
                </label>

                <textarea
                    name="about_text"
                ><?= setting("about_text", $settings) ?></textarea>

            </div>

        </div>


        <!-- FOUNDER -->

        <div class="settings-card">

            <div class="section-title">

                <h2>Founder Profile</h2>

                <p>
                    Information about the founder displayed on the website.
                </p>

            </div>


            <div class="grid">

                <div class="field">

                    <label>
                        Founder Name
                    </label>

                    <input
                        type="text"
                        name="founder_name"
                        value="<?= setting("founder_name", $settings) ?>"
                    >

                </div>


                <div class="field full">

                    <label>
                        Founder Biography
                    </label>

                    <textarea
                        name="founder_bio"
                    ><?= setting("founder_bio", $settings) ?></textarea>

                </div>

            </div>

        </div>


        <div class="save-area">

            <button
                type="submit"
                class="save-btn"
            >
                Save Settings
            </button>

        </div>


    </form>

</main>

</body>

</html>