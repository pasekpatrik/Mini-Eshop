<?php
    require '../modules/usersModul.php';

    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    $error = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_ok = $_POST['password-ok'];
        $first_name = $_POST['first-name'];
        $second_name = $_POST['second-name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $postcode = $_POST['postcode'];
        $phone = $_POST['phone'];

        $verification = verification($email, $first_name, $second_name, $address, $city, $postcode, $phone);

        if (empty($password)) {
            $verification['password'] = 'Heslo je povinné';
        } else if ($password !== $password_ok) {
            $verification['password'] = 'Hesla se neschodují';
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if (empty($verification)) {
            $id = createUser($conn, $email, $password, $first_name, $second_name, $address, $city, $postcode, $phone);

            if (!empty($id))  {
                session_regenerate_id(true);

                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $id;

                redirectUrl('/user/profile.php');
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace</title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="signUp.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container">
        <form action="#" method="POST" id="form-singup" class="check-password">
            <h2>Registrace</h2>
            <?php if ($error): ?>
            <span class="error">Registrace se nezdařila. Zkuste to znovu!</span>
            <?php endif; ?>
            <div class="one-input">
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" placeholder="jan.novak@email.cz"
                    value="<?php echo htmlspecialchars($email ?? '') ?>" required>
                <?php if (isset($verification['email'])): ?>
                <span><?php echo htmlspecialchars($verification['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input" id="passwords">
                <label for="password" class="required">Heslo</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($verification['password'])): ?>
                <span><?php echo htmlspecialchars($verification['password']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="password-ok" class="required">Potvrzení hesla</label>
                <input type="password" id="password-ok" name="password-ok" required>
                <?php if (isset($verification['password'])): ?>
                <span><?php echo htmlspecialchars($verification['password']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="first-name" class="required">Jméno</label>
                <input type="text" id="first-name" name="first-name" placeholder="Jan"
                    value="<?php echo htmlspecialchars($first_name ?? '') ?>" required>
                <?php if (isset($verification['first_name'])): ?>
                <span><?php echo htmlspecialchars($verification['first_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="second-name" class="required">Přijmení</label>
                <input type="text" id="second-name" name="second-name" placeholder="Novák"
                    value="<?php echo htmlspecialchars($second_name ?? '') ?>" required>
                <?php if (isset($verification['second_name'])): ?>
                <span><?php echo htmlspecialchars($verification['second_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="address" class="required">Ulice a č.p.</label>
                <input type="text" id="address" name="address" placeholder="Jestřábí 632"
                    value="<?php echo htmlspecialchars($address ?? '') ?>" required>
                <?php if (isset($verification['address'])): ?>
                <span><?php echo htmlspecialchars($verification['address']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="city" class="required">Město</label>
                <input type="text" id="city" name="city" placeholder="Praha"
                    value="<?php echo htmlspecialchars($city ?? '') ?>" required>
                <?php if (isset($verification['city'])): ?>
                <span><?php echo htmlspecialchars($verification['city']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="postcode" class="required">PSČ (5 čísel) </label>
                <input type="text" id="postcode" name="postcode" placeholder="100 00" pattern="[0-9]{5}"
                    value="<?php echo htmlspecialchars($postcode ?? '') ?>" required>
                <?php if (isset($verification['postcode'])): ?>
                <span><?php echo htmlspecialchars($verification['postcode']) ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="phone" class="required">Telefoní číslo (9 čísel) </label>
                <input type="tel" id="phone" name="phone" placeholder="604 366 852" pattern="[0-9]{9}"
                    value="<?php echo htmlspecialchars($phone ?? '') ?>" required>
                <?php if (isset($verification['phone'])): ?>
                <span><?php echo htmlspecialchars($verification['phone']) ?></span>
                <?php endif; ?>
            </div>
            <span>Máte uživatelský účet? <a href="logIn.php">Přihlásit se</a></span>
            <input type="submit" value="Registrovat se">
        </form>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
    <script src="../modules/js/checkPassword.js"></script>
</body>

</html>