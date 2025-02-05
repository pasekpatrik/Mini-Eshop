<?php
    require '../modules/usersModul.php';

    require '../modules/connection.php';

    session_start();
    $conn = connection();

    if (isLoggedIn()) {
        if (isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            $user = filterUserBy_($conn, 'id', $id);
        }
    
        $error = false;
        $success = false;
        $disabled = true;
    
        if (isset($_GET['change'])) {
            if ($_GET['change'] === 'able') {
                $disabled = false;
            } else {
                $disabled = true;
            }
        }
    
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('CSRF token mismatch');
            }
    
            $email = $_POST['email'];
            $first_name = $_POST['first-name'];
            $second_name = $_POST['second-name'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $postcode = $_POST['postcode'];
            $phone = $_POST['phone'];
    
            $verification = verification($email, $first_name, $second_name, $address, $city, $postcode, $phone);
    
            if (empty($verification)) {
                if (updateUser($conn, $_SESSION['user_id'], $email, $first_name, $second_name, $address, $city, $postcode, $phone)) {
                    $user = filterUserBy_($conn, 'id', $id);
                    $_SESSION['success'] = true;
    
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }
    
        if (isset($_SESSION['success'])) {
            $success = true;
            unset($_SESSION['success']);
        }
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container">
        <?php if (isLoggedIn()): ?>
        <div class="box container-password">
            <h3 id="welcome">Vítej <?php echo htmlspecialchars($user[0]['first_name']) ?> 👋</h3>
            <div id="logout-bin">
                <a href="../modules/logOut.php">Odhlášení ❌</a>
                <a href="../pages/cart.php">Do košíku 🛒</a>
            </div>
            <form action="../modules/resetPassword.php" method="POST" class="check-password">
                <h3>Změna hesla</h3>
                <?php if (isset($_GET['message'])): ?>
                <span
                    <?php echo $_GET['error'] === '1' ? "class='success'" : "class='error'" ?>><?php echo htmlspecialchars($_GET['message']) ?></span>
                <?php endif; ?>
                <div class="one-input" id="passwords">
                    <label for="password" class="required">Nové heslo</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="one-input">
                    <label for="password-ok" class="required">Potvrďte heslo</label>
                    <input type="password" id="password-ok" name="password-ok" required>
                </div>
                <input type="submit" value="Aktualizovat">
            </form>
        </div>
        <div class="box">
            <form action="profile.php?change=disable" method="POST">
                <h3>Uživatelské údaje</h3>
                <span>Chcete změnit své údaje? <a href="?change=able">Změnit</a></span>

                <?php if ($error): ?>
                <span class="error">Uživatelské údaje nebyly změněny!</span>
                <?php endif; ?>

                <?php if ($success): ?>
                <span class="success">Uživatelské údaje byly změněny.</span>
                <?php endif; ?>

                <div class="one-input">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($user[0]['email']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['email'])): ?>
                    <span><?php echo htmlspecialchars($verification['email']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="first-name">Jméno</label>
                    <input type="text" id="first-name" name="first-name"
                        value="<?php echo htmlspecialchars($user[0]['first_name']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['first_name'])): ?>
                    <span><?php echo htmlspecialchars($verification['first_name']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="second-name">Přijmení</label>
                    <input type="text" id="second-name" name="second-name"
                        value="<?php echo htmlspecialchars($user[0]['second_name']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['second_name'])): ?>
                    <span><?php echo htmlspecialchars($verification['second_name']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="address">Ulice a č.p.</label>
                    <input type="text" id="address" name="address"
                        value="<?php echo htmlspecialchars($user[0]['address']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['address'])): ?>
                    <span><?php echo htmlspecialchars($verification['address']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="city">Město</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user[0]['city']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['city'])): ?>
                    <span><?php echo htmlspecialchars($verification['city']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="postcode">PSČ (5 čísel) </label>
                    <input type="text" id="postcode" name="postcode" placeholder="100 00" pattern="[0-9]{5}"
                        value="<?php echo htmlspecialchars($user[0]['postcode']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['postcode'])): ?>
                    <span><?php echo htmlspecialchars($verification['postcode']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="one-input">
                    <label for="phone">Telefoní číslo (9 čísel) </label>
                    <input type="tel" id="phone" name="phone" placeholder="604 366 852" pattern="[0-9]{9}"
                        value="<?php echo htmlspecialchars($user[0]['phone']) ?>"
                        <?php if ($disabled) { echo 'readonly'; } ?> required>
                    <?php if (isset($verification['phone'])): ?>
                    <span><?php echo htmlspecialchars($verification['phone']); ?></span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="submit" value="Aktualizovat" <?php if ($disabled) { echo 'disabled'; } ?>>
            </form>
        </div>
        <?php else: ?>
        <span>Nepovolený přístup!</span>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
    <script src="../modules/js/checkPassword.js"></script>
</body>

</html>