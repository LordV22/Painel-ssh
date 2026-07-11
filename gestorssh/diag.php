<?php
header('Content-Type: text/plain');
echo "PHP Version: " . phpversion() . "\n";
echo "pdo_mysql: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n";
echo "mysqli: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "PDO drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n\n";

$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'IlvTDnpJyMitmGnTMJjHRTVjCWFRAxFG';
$db   = getenv('MYSQLDATABASE') ?: 'sshplus';
$port = getenv('MYSQLPORT') ?: '3306';

echo "Host: $host | DB: $db | Port: $port\n\n";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    echo "PDO Connection: OK\n";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM admin");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Admin users: " . $row['total'] . "\n";
} catch(PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
