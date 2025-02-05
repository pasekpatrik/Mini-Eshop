<?php
    require '../modules/adminModul.php';
    require '../modules/usersModul.php';
    require '../modules/productsModul.php';

    require '../modules/connection.php';
    require_once '../modules/url.php';

    session_start();
    $conn = connection();
    
    if (isLoggedInAdmin()) {
        $products = getAll_($conn, 'products');
        $users = getAll_($conn, 'users');
        $orders = getAll_($conn, 'orders');

        $message = '';

        if (isset($_GET['delete'])) {
            if ($_GET['delete'] === 'products') {
                $product = filterProductbyId($conn, $_GET['id']);
                deleteImg($product);
            }

            delete_($conn, $_GET['delete'], $_GET['id']) ? $message = 'Došlo k smazaní záznamu!' : $message = 'Nedošlo k smazaní!';

            redirectUrl("/admin/view.php?message=" . urlencode($message));
        }
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

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
    <main>
        <?php if (isLoggedInAdmin()): ?>
        <div class="container">
            <div class="box">
                <div>
                    <h2>Admin Panel</h2>
                    <a href="../modules/logOut.php">Odhlášení ❌</a>
                </div>
                <span><?php echo htmlspecialchars($_GET['message'] ?? ''); ?></span>
            </div>
            <div class="box">
                <h2>Produkty</h2>
                <div class="scroll">
                    <?php foreach ($products as $product): ?>
                    <div class="one-product-user">
                        <span
                            class="identifacion"><?php echo htmlspecialchars($product['id']); ?></span>
                        <a
                            href="view-product.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                        <a
                            href="?delete=products&id=<?php echo htmlspecialchars($product['id']); ?>">
                            <svg fill="#FF2E63" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 408.483 408.483" xml:space="preserve" stroke="#FF2E63">
                                <g stroke-width="0"></g>
                                <g stroke-linecap="round" stroke-linejoin="round"></g>
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316 H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293 c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329 c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355 c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356 c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z">
                                            </path>
                                            <path
                                                d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916 c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="box">
                <h2>Objednávky</h2>
                <div class="scroll">
                    <?php foreach ($orders as $order): ?>
                    <?php 
                        $user = filterUserBy_($conn, 'id', $order['id_user']);
                    ?>
                    <div class="one-product-user">
                        <span
                            class="identifacion"><?php echo htmlspecialchars($order['id']); ?></span>
                        <a
                            href="view-order.php?id=<?php echo htmlspecialchars($order['id']) . '&id_user=' . htmlspecialchars($order['id_user']); ?>">
                            <?php echo htmlspecialchars($user[0]['first_name'] . ' ' . $user[0]['second_name']); ?>
                        </a>
                        <span class="litle">| <?php echo htmlspecialchars($order['delivery']);?></span>
                        <a href="?delete=orders&id=<?php echo htmlspecialchars($order['id']); ?>">
                            <svg fill="#FF2E63" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 408.483 408.483" xml:space="preserve" stroke="#FF2E63">
                                <g stroke-width="0"></g>
                                <g stroke-linecap="round" stroke-linejoin="round"></g>
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316 H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293 c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329 c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355 c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356 c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z">
                                            </path>
                                            <path
                                                d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916 c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="box">
                <h2>Uživatelé</h2>
                <div class="scroll">
                    <?php foreach ($users as $user): ?>
                    <div class="one-product-user">
                        <span
                            class="identifacion"><?php echo htmlspecialchars($user['id']); ?></span>
                        <a href="view-user.php?id=<?php echo htmlspecialchars($user['id']); ?>">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </a>
                        <a href="?delete=users&id=<?php echo htmlspecialchars($user['id']); ?>">
                            <svg fill="#FF2E63" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 408.483 408.483" xml:space="preserve" stroke="#FF2E63">
                                <g stroke-width="0"></g>
                                <g stroke-linecap="round" stroke-linejoin="round"></g>
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316 H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293 c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329 c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355 c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356 c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z">
                                            </path>
                                            <path
                                                d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916 c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <a href="new-product.php">
            <div id="add">+</div>
        </a>

        <?php else: ?>
        <div class="container">
            <span>Přístup nepovolen!</span>
        </div>
        <?php endif; ?>
    </main>
    <?php require '../components/Footer/footer.php'; ?>
</body>

</html>