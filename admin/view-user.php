<?php
    require '../modules/adminModul.php';
    require '../modules/usersModul.php';
    require '../modules/connection.php';

    session_start();
    $conn = connection();
    
    if (isLoggedInAdmin()) {
        $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $user = $userId > 0 ? filterUserBy_($conn, 'id', $userId) : null;

        $error = false;
        $success = false;

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('CSRF token mismatch');
            }

            $email = $_POST["email"];
            $first_name = $_POST["first-name"];
            $second_name = $_POST["second-name"];
            $address = $_POST["address"];
            $city = $_POST["city"];
            $postcode = $_POST["postcode"];
            $phone = $_POST["phone"];
            $role = $_POST['role'];

            $verification = verification($email, $first_name, $second_name, $address, $city, $postcode, $phone);

            if (empty($verification)) {
                if (updateUser($conn, $userId, $email, $first_name, $second_name, $address, $city, $postcode, $phone, $role)) {
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
    <title>Admin panel | Uživatel <?php echo htmlspecialchars($user[0]['first_name']); ?></title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container">
        <?php if (isLoggedInAdmin()): ?>
        <form action="#" method="POST">
            <h3>Uživatel <?php echo htmlspecialchars($user[0]['first_name']); ?></h3>
            <?php if ($error): ?>
            <span class="error">Uživatel nebyl změněn!</span>
            <?php endif; ?>
            <?php if ($success): ?>
            <span class="success">Uživatel byl změněn! <a href="view.php">Zpět</a></span>
            <?php endif; ?>
            <div class="one-input">
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars($user[0]['email']); ?>" required>
                <?php if (isset($verification['email'])): ?>
                <span><?php echo htmlspecialchars($verification['email']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="first-name" class="required">Jméno</label>
                <input type="text" id="first-name" name="first-name"
                    value="<?php echo htmlspecialchars($user[0]['first_name']); ?>" required>
                <?php if (isset($verification['first_name'])): ?>
                <span><?php echo htmlspecialchars($verification['first_name']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="second-name" class="required">Přijmení</label>
                <input type="text" id="second-name" name="second-name"
                    value="<?php echo htmlspecialchars($user[0]['second_name']); ?>" required>
                <?php if (isset($verification['second_name'])): ?>
                <span><?php echo htmlspecialchars($verification['second_name']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="address" class="required">Ulice a č.p.</label>
                <input type="text" id="address" name="address"
                    value="<?php echo htmlspecialchars($user[0]['address']); ?>" required>
                <?php if (isset($verification['address'])): ?>
                <span><?php echo htmlspecialchars($verification['address']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="city" class="required">Město</label>
                <input type="text" id="city" name="city"
                    value="<?php echo htmlspecialchars($user[0]['city']); ?>" required>
                <?php if (isset($verification['city'])): ?>
                <span><?php echo htmlspecialchars($verification['city']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="postcode" class="required">PSČ (5 čísel) </label>
                <input type="text" id="postcode" name="postcode" placeholder="100 00" pattern="[0-9]{5}"
                    value="<?php echo htmlspecialchars($user[0]['postcode']); ?>" required>
                <?php if (isset($verification['postcode'])): ?>
                <span><?php echo htmlspecialchars($verification['postcode']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="phone" class="required">Telefonní číslo (9 čísel) </label>
                <input type="tel" id="phone" name="phone" placeholder="604 366 852" pattern="[0-9]{9}"
                    value="<?php echo htmlspecialchars($user[0]['phone']); ?>" required>
                <?php if (isset($verification['phone'])): ?>
                <span><?php echo htmlspecialchars($verification['phone']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="role" class="required">Role</label>
                <select name="role" id="role">
                    <option value="basic" <?php echo $user[0]['role'] === 'basic' ? 'selected' : ''; ?>>Basic
                    </option>
                    <option value="admin" <?php echo $user[0]['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="submit" value="Aktualizovat">
        </form>
        <?php else: ?>
        <span>Přístup nepovolen!</span>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>