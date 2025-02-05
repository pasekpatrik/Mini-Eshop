<?php

    /**
     * Vytvoří připojení k databázi.
     * 
     * @return mysqli Objekt připojení k databázi nebo ukončí skript při chybě.
     */
    function connection() {
        $db_host = 'localhost';
        $db_user = 'pasekpat';
        $db_password = 'webove aplikace';
        $db_name = 'pasekpat';

        $connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

        if (mysqli_connect_error()) {
            echo mysqli_connect_error();
            exit;
        }

        return $connection;
    }