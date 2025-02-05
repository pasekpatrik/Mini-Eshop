<?php
    require '../modules/usersModul.php';
    require '../modules/productsModul.php';
    require '../modules/orderModul.php';

    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    if (isLoggedIn()) {
        $items = $_SESSION['cart'] ?? [];
        $id = $_SESSION['user_id'] ?? '';
        $user = filterUserBy_($conn, 'id', $id);

        $errors = [];

        if (isset($_GET['delete'])) {
            $new_items = [];

            for ($i = 0; $i < count($items); $i++) {
                if ($items[$i]['id'] != $_GET['delete']) {
                    $new_items[] = $items[$i];
                }
            }

            $_SESSION['cart'] = $new_items;
            $items = $new_items;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['choose'])) {
                $errors['choose'] = 'Vyberte prosím jednu z možností.'; 
            } else {
                if (!empty($items)) {
                    $id_order = createOrder($conn, $id, date('Y/m/d'), $_POST['choose']);

                    foreach ($items as $item) {
                        createOrderProcuts($conn, $item['id'], $id_order);
                    }

                    redirectUrl("/pages/success.php?id=$id_order");
                } else {
                    $errors['empty'] = 'Máte prázdný košík!';
                }
            }
        }

        $items = sumItems($items);
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nákupní košík</title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container">
        <?php if (isLoggedIn()): ?>
        <div class="box">
            <div id="cart">
                <?php if (isset($errors['empty'])): ?>
                <span class="empty"><?php echo htmlspecialchars($errors['empty'])?></span>
                <?php endif; ?>
                <h2>Košík 🛒</h2>
                <?php if (!empty($items)): ?>
                <?php foreach($items as $item): ?>
                <p><span class="bold"><?php echo htmlspecialchars($item['name'])?>:
                    </span><?php echo htmlspecialchars($item['quantity'])?> Ks | Cena:
                    <?php echo htmlspecialchars($item['price']) ?> Kč
                    <a href="<?php echo htmlspecialchars("?delete=$item[id]")?>" >❌</a>
                </p>
                <?php endforeach;?>
                <br>
                <h4>Celkem: <?php echo htmlspecialchars(priceCalc($items));?> Kč</h4>
                <?php else: ?>
                <span>Žadné položky nebyly přidání do košíku!</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="box">
            <div>
                <h2>Fakturační adresa (Dodací údaje)</h2>
                <p id="info">Údaje lze změnit v <a href="../user/profile.php">profilu uživatele</a></p><br>
                <p class="bold">Jméno:
                    <?php echo htmlspecialchars($user[0]['first_name']) . ' ' . htmlspecialchars($user[0]['second_name'])?>
                </p>
                <p class="bold">Adresa:
                    <?php echo htmlspecialchars($user[0]['address']) . ', ' . htmlspecialchars($user[0]['postcode']) . ' ' . htmlspecialchars($user[0]['city'])?>
                </p>
                <p class="bold">Telefon: <?php echo htmlspecialchars($user[0]['phone']) ?></p>
            </div><br>
            <form action="#" method="POST">
                <h2>Doprava / Platba</h2>
                <?php if (isset($errors['choose'])): ?>
                <span class="error"><?php echo htmlspecialchars($errors['choose'])?></span>
                <?php endif; ?>
                <div class="one-input">
                    <input type="radio" name="choose" id="delivery" value="Na adresu(dobírka)">
                    <label for="delivery">Doručení na adresu / Dobírkou 🚚</label>
                </div>
                <div class="one-input">
                    <input type="radio" name="choose" id="subscription" value="Os. Odběr">
                    <label for="subscription">Osobní odběr / Hotově nebo kartou 🙌</label>
                </div>
                <input type="submit" value="Vytvořit objednávku">
            </form>
        </div>
        <?php else: ?>
        <div class="box" id="not-logged">
            <h2>Pro objednání produktu je nutné přihlášení!</h2>
            <a href="logIn.php">Přihlásit se</a>
        </div>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>