<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "expense_tracker";

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql_db = "CREATE DATABASE IF NOT EXISTS $dbname";
mysqli_query($conn, $sql_db);
mysqli_select_db($conn, $dbname);

// Updated Users Table with Name and Email
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    budget DECIMAL(10,2) DEFAULT 0.00
)";
mysqli_query($conn, $table_users);

// Ensure columns exist for existing databases
$columns_to_add = [
    'name' => "ALTER TABLE users ADD COLUMN name VARCHAR(100) AFTER id",
    'email' => "ALTER TABLE users ADD COLUMN email VARCHAR(100) AFTER name"
];

foreach ($columns_to_add as $col => $sql) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, $sql);
    }
}

$table_transactions = "CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
mysqli_query($conn, $table_transactions);
?>