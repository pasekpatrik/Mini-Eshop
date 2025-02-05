<?php 
    require '../modules/productsModul.php';
    require '../modules/usersModul.php';

    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();

    $limit = 6;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $products = listProducts($conn, $_GET['category'], $limit, $offset);
    $totalProducts = count(listProducts($conn, $_GET['category']));
    $totalPages = ceil($totalProducts / $limit);
    
    if (isLoggedIn()) {
        if (isset($_GET['id'])) {
            $product = filterProductbyId($conn, $_GET['id']);
            $items = $_SESSION['cart'] ?? [];
    
            $item = ['id' => $product[0]['id'],'quantity' =>  1, 'name' => $product[0]['name'], 'price' => $product[0]['price']];
            $items[] = $item;
    
            $_SESSION['cart'] = $items;

            redirectUrl("/pages/products.php?category=$_GET[category]");
        }
    } else {
        if (isset($_GET['id'])) {
            redirectUrl("/pages/products.php?category=$_GET[category]");
        }
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkty | <?php echo htmlspecialchars($_GET['category']);?></title>

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/global.css">

    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="../components/NavBar/navbar.css">
    <link rel="stylesheet" href="../components/Footer/footer.css">
</head>

<body>
    <header>
        <?php require '../components/NavBar/navbar.php'; ?>
    </header>
    <main class="container" id="container-products-pages">
        <div id="container-products">
            <?php if (!empty($products)): ?>
            <?php foreach($products as $product): ?>
            <div class="products">
                <div class="one-product">
                    <img src="<?php echo htmlspecialchars($product['imgPath']) ?>"
                        alt="<?php echo htmlspecialchars($product['name'])?>" width="90" height="90">
                    <h2><?php echo htmlspecialchars($product['name'])?></h2>
                    <h4>Kategorie: <?php echo htmlspecialchars($product['category']);?></h4>
                    <span><?php echo htmlspecialchars($product['price'])?> Kč</span>
                    <p><?php echo htmlspecialchars($product['description'])?></p>
                </div>
                <div class="container-products-btn">
                    <a href="<?php echo htmlspecialchars("?category=$product[category]&id=$product[id]") ?>"
                        class="btn">Do košíku</a>
                </div>
            </div>
            <?php endforeach;?>
            <?php else: ?>
            <span>Produkty nebyli nalezeny!</span>
            <?php endif; ?>
            </div>

        <div class="pages">
            <ul>
                <?php if ($page > 1): ?>
                <li><a href="?category=<?php echo $_GET['category']?>&page=<?php echo $page - 1 ?>"><</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <li>
                        <a href="?category=<?php echo $_GET['category']?>&page=<?php echo $i ?>" class="current-page"><?php echo $i ?></a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="?category=<?php echo $_GET['category']?>&page=<?php echo $i ?>"><?php echo $i ?></a>
                    </li>
                <?php endif;?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li><a href="?category=<?php echo $_GET['category']?>&page=<?php echo $page + 1 ?>">></a>
                </li>
                <?php endif; ?>
            </ul>
            </div>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>