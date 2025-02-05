<?php
/**
 * Vytvoří nového uživatele v databázi.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $email Email uživatele.
 * @param string $password Heslo uživatele.
 * @param string $first_name Křestní jméno uživatele.
 * @param string $second_name Příjmení uživatele.
 * @param string $address Adresa uživatele.
 * @param string $city Město uživatele.
 * @param string $postcode PSČ uživatele.
 * @param string $phone Telefonní číslo uživatele.
 * @param string $role Role uživatele (default: "basic").
 * 
 * @return int|false ID nově vytvořeného uživatele v případě úspěchu, false v případě chyby.
 */
function createUser($connection, $email, $password, $first_name, $second_name, $address, $city, $postcode, $phone, $role = "basic") {
    $sql = "INSERT INTO users (email, password, first_name, second_name, address, city, postcode, phone, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
    } else {
        mysqli_stmt_bind_param($statement, 'sssssssss', $email, $password, $first_name, $second_name, $address, $city, $postcode, $phone, $role);

        if (mysqli_stmt_execute($statement)) {
            $id = mysqli_insert_id($connection);
            mysqli_stmt_close($statement);
            return $id;
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
        }
    }
}

/**
 * Ověřuje, zda je uživatel přihlášený nebo ne.
 * 
 * @return boolean True pokud je uživatel přihlášený, jinak false.
 */
function isLoggedIn() {
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
}

/**
 * Ověřuje přihlášení uživatele na základě emailu a hesla.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $email Email uživatele.
 * @param string $password Heslo uživatele.
 * 
 * @return boolean True pokud je uživatel úspěšně ověřen, jinak false.
 */
function auth($connection, $email, $password) {
    $sql = "SELECT password
            FROM users
            WHERE email = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
    } else {
        mysqli_stmt_bind_param($statement, 's', $email);

        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            $password_db = mysqli_fetch_row($result);

            if ($password_db) {
                mysqli_stmt_close($statement);
                return password_verify($password, $password_db[0]);
            }
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
        }
    }
}

/**
 * Získá ID uživatele na základě emailu.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $email Email uživatele.
 * 
 * @return int|false ID uživatele, nebo false při chybě.
 */
function getUserId($connection, $email) {
    $sql = "SELECT id
            FROM users
            WHERE email = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
    } else {
        mysqli_stmt_bind_param($statement, 's', $email);

        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            $id_db = mysqli_fetch_row($result);

            mysqli_stmt_close($statement);
            return $id_db[0];
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
        }
    }
}

/**
 * Filtruje uživatele podle specifikovaného parametru.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param string $what Sloupec, podle kterého se filtruje (např. 'email').
 * @param string $param Hodnota pro filtraci.
 * 
 * @return array Seznam uživatelů odpovídajících kritériím.
 */
function filterUserBy_($connection, $what, $param) {
    $sql = "SELECT * 
            FROM users
            WHERE $what = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
    } else {
        mysqli_stmt_bind_param($statement, 's', $param);

        if (mysqli_stmt_execute($statement)) {
            $result = mysqli_stmt_get_result($statement);
            $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_stmt_close($statement);
            return $array;
        } else {
            echo mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
        }
    }
}

/**
 * Aktualizuje údaje o uživatelském účtu.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param int $id ID uživatele.
 * @param string $email Email uživatele.
 * @param string $first_name Křestní jméno uživatele.
 * @param string $second_name Příjmení uživatele.
 * @param string $address Adresa uživatele.
 * @param string $city Město uživatele.
 * @param string $postcode PSČ uživatele.
 * @param string $phone Telefonní číslo uživatele.
 * @param string $role Role uživatele.
 * 
 * @return boolean True pokud bylo úspěšně aktualizováno, jinak false.
 */
function updateUser($connection, $id, $email, $first_name, $second_name, $address, $city, $postcode, $phone, $role = 'basic') {
    $sql = "UPDATE users
            SET email = ?, first_name = ?, second_name = ?, address = ?, city = ?, postcode = ?, phone = ?, role = ?
            WHERE id = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'sssssssss', $email, $first_name, $second_name, $address, $city, $postcode, $phone, $role ,$id);

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
 * Aktualizuje heslo uživatele.
 * 
 * @param mysqli $connection Připojení k databázi.
 * @param int $id ID uživatele.
 * @param string $password Nové heslo.
 * 
 * @return boolean True pokud bylo heslo úspěšně aktualizováno, jinak false.
 */
function updatePassword($connection, $id, $password) {
    $sql = "UPDATE users
            SET password = ?
            WHERE id = ?";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        echo mysqli_error($connection);
        return false;
    } else {
        mysqli_stmt_bind_param($statement, 'ss', $password, $id);

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
 * Validuje údaje při registraci uživatele.
 * 
 * @param string $email Email uživatele.
 * @param string $first_name Křestní jméno uživatele.
 * @param string $second_name Příjmení uživatele.
 * @param string $address Adresa uživatele.
 * @param string $city Město uživatele.
 * @param string $postcode PSČ uživatele.
 * @param string $phone Telefonní číslo uživatele.
 * 
 * @return array Seznam chyb, pokud jsou přítomny.
 */
function verification($email, $first_name, $second_name, $address, $city, $postcode, $phone) {
    $errors = [];

    if (empty($email)) {
        $errors['email'] = "Email je povinný.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email nemá platný formát.";
    }

    if (empty($first_name)) {
        $errors['first_name'] = "Jméno je povinné.";
    }

    if (empty($second_name)) {
        $errors['second_name'] = "Přijmení je povinné.";
    }

    if (empty($address)) {
        $errors['address'] = "Adresa je povinná.";
    }

    if (empty($city)) {
        $errors['city'] = "Město je povinné.";
    }

    if (empty($postcode)) {
        $errors['postcode'] = "PSČ je povinné.";
    } elseif (!is_numeric($postcode)) {
        $errors['postcode'] = "PSČ musí být číslo.";
    } elseif (strlen($postcode) < 5) {
        $errors['postcode'] = "PSČ musí být dlouhý 5 znaků.";
    }

    if (empty($phone)) {
        $errors['phone'] = "Telefon je povinný.";
    } elseif (!is_numeric($phone)) {
        $errors['phone'] = "Telefon musí být číslo.";
    } elseif (strlen($phone) < 9) {
        $errors['phone'] = "Telefon musí být dlouhý 9 znaků.";
    }

    return $errors;
}