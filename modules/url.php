<?php
    /**
 * Přesměrovává uživatele na zadanou URL.
 * 
 * @param string $path Cesta, kam má být uživatel přesměrován (např. "/stranka").
 * 
 * @return void
 */
function redirectUrl($path) {
    // Určení protokolu (http nebo https).
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $url_protocol = 'https';
    } else {
        $url_protocol = 'http';
    }

    // Sestavení kompletní URL a přesměrování.
    header("Location: $url_protocol://" . $_SERVER['HTTP_HOST'] . "/~pasekpat" . $path);
    exit; // Zastavení skriptu po přesměrování.
}
