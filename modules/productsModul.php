<?php

/**
 * Filtruje produkty podle zadané kategorie.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $category Kategorie produktů.
 * 
 * @return array Pole produktů odpovídajících kategorii.
 */
function filterProductsbyCategory($connection, $category) {
    $sql = "SELECT * 
            FROM products
            WHERE category = ?";

    $statement = mysqli_prepare($connection, $sql);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 's', $category);

        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } else {
        echo mysqli_error($connection);
    }
}

/**
 * Filtruje produkt podle zadaného ID.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $id ID produktu.
 * 
 * @return array Pole s informacemi o produktu.
 */
function filterProductbyId($connection, $id) {
    $sql = "SELECT * 
            FROM products
            WHERE id = ?";

    $statement = mysqli_prepare($connection, $sql);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 's', $id);

        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } else {
        echo mysqli_error($connection);
    }
}

/**
 * Aktualizuje informace o produktu v databázi.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $id ID produktu.
 * @param string $category Kategorie produktu.
 * @param string $name Název produktu.
 * @param string $price Cena produktu.
 * @param string $description Popis produktu.
 * @param string $imgPath Cesta k obrázku produktu.
 * 
 * @return bool true, pokud aktualizace proběhla úspěšně, jinak false.
 */
function updateProduct($connection, $id, $category, $name, $price, $description, $imgPath) {
    $sql = "UPDATE products
            SET category = ?, name = ?, price = ?, description = ?, imgPath = ?
            WHERE id = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'ssssss', $category, $name, $price, $description, $imgPath, $id);

        if (mysqli_stmt_execute($statement)) {
            mysqli_stmt_close($statement);
            return true;
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
            return false;
        }
    }
}

/**
 * Ověřuje údaje o produktu a vrací pole s případnými chybami.
 * 
 * @param string $name Název produktu.
 * @param string $price Cena produktu.
 * @param string $category Kategorie produktu.
 * @param string $description Popis produktu.
 * @param array $img Informace o obrázku produktu.
 * 
 * @return array Pole s chybovými zprávami.
 */
function verifyProduct($name, $price, $category, $description, $img) {
    $errors = [];

    if (empty($name)) {
        $errors['name'] = "Název produktu je povinný.";
    } elseif (strlen($name) > 255) {
        $errors['name'] = "Název produktu je příliš dlouhý (max. 255 znaků).";
    }

    if (empty($price)) {
        $errors['price'] = "Cena produktu je povinná.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $errors['price'] = "Cena produktu musí být kladné číslo.";
    }

    $allowedCategories = ['fotbal', 'basketbal', 'hokej'];
    
    if (empty($category)) {
        $errors['category'] = "Kategorie je povinná.";
    } elseif (!in_array($category, $allowedCategories)) {
        $errors['category'] = "Kategorie není platná.";
    }

    if (empty($description)) {
        $errors['description'] = "Popis produktu je povinný.";
    } elseif (strlen($description) < 10) {
        $errors['description'] = "Popis produktu musí mít alespoň 10 znaků.";
    }

    if (!empty($img['name'])) {
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        if (!in_array($img['type'], $allowedMimeTypes)) {
            $errors['img'] = "Obrázek musí být ve formátu JPEG nebo PNG.";
        } elseif ($img['size'] > 5 * 1024 * 1024) {
            $errors['img'] = "Velikost obrázku nesmí překročit 5 MB.";
        }
    }

    return $errors;
}

/**
 * Změní velikost obrázku a uloží jej na zadanou cestu.
 * 
 * @param string $filename Cesta k původnímu obrázku.
 * @param string $destinationPath Cesta k uložení upraveného obrázku.
 */
function resizeImgAndUpload($filename, $destinationPath) {
    list($width, $height, $imageType) = getimagesize($filename);
    $new_width = 90;
    $new_height = 90;

    $thumb = imagecreatetruecolor($new_width, $new_height);

    if ($imageType == IMAGETYPE_JPEG){
        $sourceImage = imagecreatefromjpeg($filename);
    } else if ($imageType == IMAGETYPE_PNG) {
        $sourceImage = imagecreatefrompng($filename);

        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);

        $transparentColor = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $transparentColor);
    }

    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    if ($imageType == IMAGETYPE_JPEG){
        imagejpeg($thumb, $destinationPath, 90);
    } else if ($imageType == IMAGETYPE_PNG) {
        imagepng($thumb, $destinationPath, 9);
    }
}

/**
 * Smaže obrázek produktu, pokud nejde o výchozí obrázek.
 * 
 * @param array $product Pole s informacemi o produktu.
 */
function deleteImg($product) {
    $path = $product[0]['imgPath'];

    if ($path !== '../uploads/default.png') {
        unlink($path);
    }
}

/**
 * Vypočítá celkovou cenu položek.
 * 
 * @param array $items Pole položek s cenami.
 * 
 * @return int Celková cena.
 */
function priceCalc($items) {
    $sum = 0;

    foreach ($items as $item) {
        $sum += (int)$item['price'];
    }

    return $sum;
}

/**
 * Získá produkty zadané kategorie s možností omezení počtu a posunu.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $category Kategorie produktů.
 * @param int|null $limit Počet produktů k načtení.
 * @param int $offset Posun v seznamu produktů.
 * 
 * @return array Pole produktů.
 */
function listProducts($connection, $category, $limit = null, $offset = 0) {
    $products = filterProductsbyCategory($connection, $category);

    if ($limit !== null) {
        $products = array_slice($products, $offset, $limit);
    }

    return $products;
}

/**
 * Slučuje duplikáty položek a přepočítává jejich množství a cenu.
 * 
 * @param array $items Pole položek.
 * 
 * @return array Pole sloučených položek.
 */
function sumItems($items, $by = 'id') {
    $new_array = [];

    for ($i = 0; $i < count($items); $i++) {
        $item = ['id' => $items[$i][$by],'quantity' =>  1, 'name' =>$items[$i]['name'], 'price' => $items[$i]['price']];

        for ($j = 0; $j < count($items); $j++) {
            if ($item['id'] === $items[$j][$by] && $i !== $j) {
                $item['quantity']++;
                $item['price'] +=  $items[$j]['price'];
            }
        }

        if (!in_array($item, $new_array)) {
            $new_array[] = $item;
        }
    }

    foreach ($new_array as &$item) {
        $item['price'] = number_format((float)$item['price'], 2, '.', '');
    }

    return $new_array;
}