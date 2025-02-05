<?php
    require '../modules/adminModul.php';
    require '../modules/productsModul.php';
    require '../modules/connection.php';

    session_start();
    $conn = connection();

    if (isLoggedInAdmin()) {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $product = $productId > 0 ? filterProductbyId($conn, $productId) : null;
    
        $error = false;
        $success = false;
    
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('CSRF token mismatch');
            }
    
            $name = $_POST["name"];
            $price = $_POST["price"];
            $category = $_POST["category"];
            $description = $_POST["description"];
            $img = $_FILES['img'] ?? null;
    
            $verification = verifyProduct($name, $price, $category, $description, $img);
    
            if (empty($verification)) {
                $imgPath = $product[0]['imgPath'];
    
                if (!empty($img['name'])) {
                    $fileName = $img['name'];
                    $fileTmpName = $img['tmp_name'];
                    $imgPath = '../uploads/' . uniqid() . '-' . basename($img['name']);
                    deleteImg($product);
                    resizeImgAndUpload($fileTmpName,  $imgPath);
                }
    
                if (updateProduct($conn, $productId, $category, $name, $price, $description, $imgPath)) {
                    $_SESSION['success'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
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
    <title>Admin panel | Produkt <?php echo htmlspecialchars($product[0]['name'], ENT_QUOTES, 'UTF-8'); ?></title>

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
            <h3><?php echo htmlspecialchars($product[0]['name']); ?></h3>
            <?php if ($error): ?>
            <span class="error">Produkt nebyl změněn!</span>
            <?php endif; ?>
            <?php if ($success): ?>
            <span class="success">Produkt byl změněn! <a href="view.php">Zpět</a></span>
            <?php endif; ?>
            <div class="one-input">
                <label for="name" class="required">Jméno produktu</label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($product[0]['name']); ?>" required>
                <?php if (isset($verification['name'])): ?>
                <span><?php echo htmlspecialchars($verification['name']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="price" class="required">Cena produktu</label>
                <input type="number" id="price" name="price"
                    value="<?php echo htmlspecialchars($product[0]['price']); ?>" required>
                <?php if (isset($verification['price'])): ?>
                <span><?php echo htmlspecialchars($verification['price']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="category" class="required">Kategorie</label>
                <select name="category" id="category">
                    <option value="fotbal" <?php echo $product[0]['category'] === 'fotbal' ? 'selected' : ''; ?>>Fotbal
                    </option>
                    <option value="basketbal" <?php echo $product[0]['category'] === 'basketbal' ? 'selected' : ''; ?>>
                        Basketbal</option>
                    <option value="hokej" <?php echo $product[0]['category'] === 'hokej' ? 'selected' : ''; ?>>Hokej
                    </option>
                </select>
                <?php if (isset($verification['category'])): ?>
                <span><?php echo htmlspecialchars($verification['category']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="img">Obrázek produktu</label>
                <input type="file" name="img" id="img" accept="image/png, image/jpeg">
                <?php if (isset($verification['img'])): ?>
                <span><?php echo htmlspecialchars($verification['img']); ?></span>
                <?php endif; ?>
            </div>
            <div class="one-input">
                <label for="description" class="required">Popis</label>
                <textarea name="description" id="description" rows="8" cols="8"
                    required><?php echo htmlspecialchars(trim($product[0]['description'])); ?></textarea>
                <?php if (isset($verification['description'])): ?>
                <span><?php echo htmlspecialchars($verification['description']); ?></span>
                <?php endif; ?>
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