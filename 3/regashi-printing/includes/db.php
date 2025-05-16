<?php
/**
 * Database Connection File
 * Regashi Printing Website
 */

// Include configuration file
require_once 'config.php';

// Attempt to connect to MySQL database
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Set character set to utf8mb4
    $pdo->exec("SET NAMES utf8mb4");
    
} catch(PDOException $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}

// Function to create a new database if it doesn't exist
function createDatabase() {
    try {
        $tempPdo = new PDO("mysql:host=" . DB_SERVER, DB_USERNAME, DB_PASSWORD);
        $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create the database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $tempPdo->exec($sql);
        
        echo "Database created successfully or already exists.<br>";
        return true;
    } catch(PDOException $e) {
        die("ERROR: Could not create database. " . $e->getMessage());
    }
}

// Function to initialize the database tables
function initializeDatabase($pdo) {
    try {
        // Read the SQL from schema.sql in the root directory
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        
        // Execute the SQL
        $pdo->exec($sql);
        
        echo "Database tables created successfully.<br>";
        return true;
    } catch(PDOException $e) {
        die("ERROR: Could not initialize database tables. " . $e->getMessage());
    }
}

// Check if the database needs initialization
function checkDatabaseInitialization($pdo) {
    try {
        // Check if the users table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Table doesn't exist, initialize the database
            initializeDatabase($pdo);
        }
    } catch(PDOException $e) {
        // If there's an error, the database might not exist
        if ($e->getCode() == 1049) { // 1049 is "Unknown database"
            createDatabase();
            // After creating the database, we need to reconnect and initialize
            $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            initializeDatabase($pdo);
        } else {
            die("ERROR: " . $e->getMessage());
        }
    }
}

// Only run this during setup
if (isset($_GET['setup']) && $_GET['setup'] == 'true') {
    checkDatabaseInitialization($pdo);
}