<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function hasRole(string $role): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}
