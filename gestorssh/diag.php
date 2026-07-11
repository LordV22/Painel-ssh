<?php
header('Content-Type: text/plain');
echo "PHP Version: " . phpversion() . "\n";
echo "PDO drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
echo "Extension loaded (pdo_mysql): " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n";
echo "Extension loaded (mysqli): " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "\n";

// Test connection
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'IlvTDnpJyMitmGnTMJjHRTVjCWFRAxFG';
$db   = getenv('MYSQLDATABASE') ?: 'sshplus';
$port = getenv('MYSQLPORT') ?: '3306';

echo "Host: $host\nUser: $user\nDB: $db\nPort: $port\n\n";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    echo "PDO Connection: OK\n";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM admin");
    $row = $stmt->fetch();
    echo "Admin users: " . $row['total'] . "\n";
} catch(PDOException $e) {
    echo "PDO Connection FAILED: " . $e->getMessage() . "\n";
}
?>
