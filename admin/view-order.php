<?php
    require '../modules/adminModul.php';
    require '../modules/usersModul.php';
    require '../modules/connection.php';
    require '../modules/orderModul.php';
    require '../modules/productsModul.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    if (isLoggedInAdmin()) {
        if (isset($_GET['id']) && isset($_GET['id_user'])) {
            $order = getOrder($conn, $_GET['id']);
            $user = filterUserBy_($conn, 'id', $_GET['id_user']);
        } else {
            redirectUrl("/admin/view.php");  
        }
    
        $order = sumItems($order, 'id_products');
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Objednávka</title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="view.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container">
        <?php if (isLoggedInAdmin()): ?>
        <div class="box">
            <div>
                <h2>Obsah objednávky</h2>
                <?php foreach($order as $item): ?>
                <p><span class="bold"><?php echo htmlspecialchars($item['name'])?></span>: <?php echo htmlspecialchars($item['quantity'])?> Ks | Cena:
                    <?php echo htmlspecialchars($item['price']) ?> Kč </p>
                <?php endforeach;?><br>
                <h4>Celkem: <?php echo htmlspecialchars(priceCalc($order));?> Kč</h4><br>
            </div>
        </div>
        <div class="box">
            <div>
                <h2>Fakturační adresa</h2>
                <p class="bold">Jméno:
                    <?php echo htmlspecialchars($user[0]['first_name']) . ' ' . htmlspecialchars($user[0]['second_name'])?>
                </p>
                <p class="bold">Adresa:
                    <?php echo htmlspecialchars($user[0]['address']) . ', ' . htmlspecialchars($user[0]['postcode']) . ' ' . htmlspecialchars($user[0]['city'])?>
                </p>
                <p class="bold">Telefon: <?php echo htmlspecialchars($user[0]['phone']) ?></p>
            </div>
        </div>
        <?php else: ?>
        <span>Přístup nepovolen!</span>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>