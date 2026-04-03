<?php

function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function validateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        return false;
    }
    // Token nach Verwendung erneuern
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}
