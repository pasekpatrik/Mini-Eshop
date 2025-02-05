<?php

/**
 * Vytvoří nový záznam objednávky v tabulce "orders".
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $id_user ID uživatele, který objednávku vytvořil.
 * @param string $date Datum vytvoření objednávky.
 * @param string $delivery Informace o doručení.
 * 
 * @return int|false ID vytvořené objednávky nebo false v případě chyby.
 */
function createOrder($connection, $id_user, $date, $delivery) {
    $sql = "INSERT INTO orders (id_user, date, delivery)
            VALUE (?, ?, ?)";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'sss', $id_user, $date, $delivery);

        if (mysqli_stmt_execute($statement)) {
            $id = mysqli_insert_id($connection);
            mysqli_stmt_close($statement);
            return $id;
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
            return false;
        }
    }
}

/**
 * Vytvoří záznam o vztahu mezi produktem a objednávkou v tabulce "orders_products".
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $id_products ID produktu.
 * @param string $id_orders ID objednávky.
 * 
 * @return bool true, pokud byl záznam úspěšně vytvořen, jinak false.
 */
function createOrderProcuts($connection, $id_products, $id_orders) {
    $sql = "INSERT INTO orders_products (id_products, id_orders)
            VALUE (?, ?)";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'ss', $id_products, $id_orders);

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
 * Získá podrobnosti o objednávce podle ID.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $id ID objednávky.
 * 
 * @return array|false Pole záznamů o produktech v objednávce, nebo false v případě chyby.
 */
function getOrder($connection, $id) {
    $sql = "SELECT id_products, category, name, price
            FROM orders_products
            INNER JOIN orders ON orders.id = orders_products.id_orders
            INNER JOIN products ON products.id = orders_products.id_products
            WHERE id_orders = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 's', $id);

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
