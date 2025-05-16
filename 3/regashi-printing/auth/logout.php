<?php
/**
 * Logout Script
 * Regashi Printing Website
 */

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_destroy();

// Redirect to homepage
header("Location: " . SITE_URL . "/index.php");
exit;