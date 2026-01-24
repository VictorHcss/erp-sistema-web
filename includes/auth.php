<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

if (!function_exists('hasRole')) {
    function hasRole($role)
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
}

function getCompanyId()
{
    return $_SESSION['company_id'] ?? null;
}
