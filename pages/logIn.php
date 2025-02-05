<?php 
    require '../modules/usersModul.php';
    require '../modules/adminModul.php';

    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    $errors = [];
    $error = false;

    if (isLoggedIn()) {
        redirectUrl('/user/profile.php');
    } else if (isLoggedInAdmin()) {
        redirectUrl('/admin/view.php');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email)) {
            $errors['email'] = 'Email je povinný.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email nemá platný formát.';
        }

        if (empty($password)) {
            $errors['password'] = 'Heslo je povinné.';
        }

        if (empty($errors)) {
            if (auth($conn, $email, $password)) {
                $id = getUserId($conn, $email);
                $user = filterUserBy_($conn, "id", $id);
    
                session_regenerate_id(true);
    
                if ($user[0]["role"] === 'basic') {
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['user_id'] = $id;
    
                    redirectUrl('/user/profile.php');
                } else if ($user[0]["role"] === 'admin') {
                    $_SESSION['is_logged_in_admin'] = true;
                    $_SESSION['admin_id'] = $id;
    
                    redirectUrl('/admin/view.php');
                } else {
                    $error = true;
                }
            } else {
                $error = true;
                $errors['password'] = 'Heslo je zadáno špatně.';
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
    <title>Přihlášení</title>

    <link rel="shortcut icon" href="/~pasekpat/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="logIn.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container" id="container-login">
        <form action="#" method="POST" id="form-login">
            <h2>Přihlášení</h2>
            <div class="one-input">
                <label for="email" class="required">E-mail</label>
                <input type="email" name="email" id="email" placeholder="jan.novak@email.cz"
                    value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?>
                <span><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="password" class="required">Heslo</label>
                <input type="password" name="password" id="password" required>
                <?php if (isset($errors['password'])): ?>
                <span><?php echo htmlspecialchars($errors['password']); ?></span>
                <?php endif; ?>
            </div>
            <span>Nemáte uživatelský učet? <a href="signUp.php">Zaregistrujte se</a></span>
            <input type="submit" value="Přihlásit">
            <?php if ($error):?>
            <span class="error">Přihlášení se nezdařilo. Zkuste to znovu!</span>
            <?php endif; ?>
        </form>
    </main>
    <?php require '../components/Footer/footer.php' ?>
</body>

</html>