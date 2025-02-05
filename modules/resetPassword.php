<?php
    require 'usersModul.php';
    require 'connection.php';
    require_once 'url.php';

    session_start();
    $conn = connection();

    if (isLoggedIn() && isset($_SESSION['user_id'])) {
        if (!isset($_POST['password']) && !isset($_POST['password-ok'])) {
            redirectUrl("/");
        }

        $password = $_POST['password'];
        $newPassword = $_POST['password-ok'];
        $message = '';
        $error = false;

        if (empty($password) || empty($newPassword)) {
            $message = 'Všechna pole musí být vyplňena!';
        } else {
            if ($password === $newPassword) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                if(updatePassword($conn, $_SESSION['user_id'], $password)) {
                    $message = 'Heslo bylo změněno.';
                    $error = true;
                } else {
                    $message = 'Heslo nebylo změněno!';
                }
            } else {
                $message = 'Hesla se neschodují!';
            }
        }

        redirectUrl("/user/profile.php?message=$message&error=$error");
    } else {
        redirectUrl("/");
    }

    