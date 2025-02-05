<?php
require '../modules/connection.php';
require '../modules/adminModul.php';
require '../modules/productsModul.php';

session_start();
$conn = connection();

if (isLoggedInAdmin()) {
    $error = false;
    $success = false;
    $verification = [];

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token mismatch');
        }

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $category = $_POST['category'] ?? '';
        $img = $_FILES['img'] ?? null;
        $description = $_POST['description'] ?? '';

        $verification = verifyProduct($name, $price, $category, $description, $img);

        if (empty($verification)) {
            $imgPath = '../uploads/default.png';

            if (!empty($img['name'])) {
                $fileName = $img['name'];
                $fileTmpName = $img['tmp_name'];
                $imgPath = '../uploads/' . uniqid() . '-' . basename($img['name']);
                resizeImgAndUpload($fileTmpName, $imgPath); 
            }

            if (empty($verification)) {
                if (createProduct($conn, $name, $price, $category, $imgPath, $description)) {
                    $_SESSION['success'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    $error = true;
                }
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
    <title>Admin Panel | Přidání produktu</title>

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
        <form action="#" method="POST" enctype="multipart/form-data">
            <h3>Nový produkt</h3>
            <?php if ($error): ?>
            <span class="error">Produkt nebyl vytvořen!</span>
            <?php endif; ?>
            <?php if ($success): ?>
            <span class="success">Produkt byl úspěšně vytvořen! <a href="view.php">Zpět</a></span>
            <?php endif; ?>
            <div class="one-input">
                <label for="name" class="required">Jméno produktu</label>
                <input type="text" id="name" name="name" placeholder="Míč Euro 2024"
                    value="<?php echo htmlspecialchars($name ?? '') ?>" required>
                <?php if (isset($verification['name'])): ?>
                <span><?php echo htmlspecialchars($verification['name']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="price" class="required">Cena produktu</label>
                <input type="number" id="price" name="price" placeholder="10 000"
                    value="<?php echo htmlspecialchars($price ?? '') ?>" required>
                <?php if (isset($verification['price'])): ?>
                <span><?php echo htmlspecialchars($verification['price']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="category" class="required">Kategorie</label>
                <select name="category" id="category">
                    <option value="fotbal" <?php echo isset($category) && $category === 'fotbal' ? 'selected' : '' ?>>
                        Fotbal</option>
                    <option value="basketbal"
                        <?php echo isset($category) && $category === 'basketbal' ? 'selected' : '' ?>>Basketbal</option>
                    <option value="hokej" <?php echo isset($category) && $category === 'hokej' ? 'selected' : '' ?>>
                        Hokej</option>
                </select>
                <?php if (isset($verification['category'])): ?>
                <span><?php echo htmlspecialchars($verification['category']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="img">Obrázek produktu</label>
                <input type="file" name="img" id="img">
                <?php if (isset($verification['img'])): ?>
                <span><?php echo htmlspecialchars($verification['img']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="description" class="required">Popis</label>
                <textarea name="description" id="description" placeholder="Popis..." rows="8"
                    cols="8" required><?php echo htmlspecialchars($description ?? '') ?></textarea>
                <?php if (isset($verification['description'])): ?>
                <span><?php echo htmlspecialchars($verification['description']); ?></span>
                <?php endif; ?>
            </div>
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="submit" value="Odeslat">
        </form>
        <?php else: ?>
        <span>Přístup nepovolen!</span>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>