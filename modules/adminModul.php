<?php

/**
 * Ověřuje, zda je uživatel přihlášený jako administrátor.
 * 
 * @return bool true, pokud je uživatel přihlášený jako admin, jinak false.
 */
function isLoggedInAdmin() {
    return isset($_SESSION['is_logged_in_admin']) && $_SESSION['is_logged_in_admin'];
}

/**
 * Získá všechny záznamy z dané tabulky v databázi.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $table Název tabulky, ze které chceme data získat.
 * 
 * @return array|false Pole asociativních záznamů z tabulky, nebo false v případě chyby.
 */
function getAll_($connection, $table) {
    $sql = "SELECT * FROM $table";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            $array = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($statement);
            return $array;
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
            return false;
        }
    }
}

/**
 * Vytvoří nový produkt v tabulce "products".
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $name Název produktu.
 * @param string $price Cena produktu.
 * @param string $category Kategorie produktu.
 * @param string $imgPath Cesta k obrázku produktu.
 * @param string $description Popis produktu.
 * 
 * @return bool true, pokud byl produkt úspěšně vytvořen, jinak false.
 */
function createProduct($connection, $name, $price, $category, $imgPath, $description) {
    $sql = "INSERT INTO products (name, price, category, imgPath, description) VALUE (?, ?, ?, ?, ?)";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'sssss', $name, $price, $category, $imgPath, $description);

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
 * Smaže záznam z dané tabulky podle ID.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $table Název tabulky (povolené jsou pouze "users", "products" nebo "orders").
 * @param string $id ID záznamu, který chceme smazat.
 * 
 * @return bool true, pokud byl záznam úspěšně smazán, jinak false.
 */
function delete_($connection, $table, $id) {
    if ($table === 'users' || $table === 'products' || $table === 'orders') {
        $sql = "DELETE FROM $table WHERE id = ?";

        $statement = mysqli_prepare($connection, $sql);

        if (!$statement) {
            echo mysqli_error($connection);
            return false;
        } else {
            mysqli_stmt_bind_param($statement, 's', $id);

            if (mysqli_stmt_execute($statement)) {
                mysqli_stmt_close($statement);
                return true;
            } else {
                echo mysqli_stmt_error($statement);
                mysqli_stmt_close($statement);
                return false;
            }
        }
    } else {
        return false;
    }
}
