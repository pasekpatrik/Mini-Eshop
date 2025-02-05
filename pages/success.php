<?php
    require '../modules/usersModul.php';
    require '../modules/productsModul.php';
    require '../modules/orderModul.php';
    
    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    if (isLoggedIn()) {
        unset($_SESSION['cart']);

        if (isset($_GET['id'])) {
            $order = getOrder($conn, $_GET['id']);
        } else {
            redirectUrl("/");
        }

        $order = sumItems($order, 'id_products');
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objednání produktu</title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="success.css">
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
                <div>
                    <h2>Souhrn objednávky</h2>
                    <p>Vážený zákazníku, děkujeme Vám za objednávku a potvrzujeme její přijetí.</p><br>
                    <h2>Obsah objednávky:</h2>
                    <?php foreach($order as $item): ?>
                    <p><span class="bold"><?php echo htmlspecialchars($item['name'])?></span>: <?php echo htmlspecialchars($item['quantity'])?> Ks | Cena: <?php echo htmlspecialchars($item['price']) ?> Kč </p>
                    <?php endforeach;?><br>
                    <h4>Celkem: <?php echo htmlspecialchars(priceCalc($order));?> Kč</h4><br>
                    <a href="../">Zpět na úvodní stránku</a>
                </div>
            </div>
            <div class="box">
                <img src="../images/success.svg" alt="Zboží bylo zaevidováno" id="success">
            </div>
        <?php else: ?>
        <div class="box">
            <h4>Pro objednání produktu je nutné přihlášení!</h4>
            <a href="logIn.php">Přihlásit se</a>
        </div>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>