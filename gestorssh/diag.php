<?php
header('Content-Type: text/plain');
echo "PHP Version: " . phpversion() . "\n";
echo "Extension (mysqli): " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "Extension (pdo): " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "Extension (pdo_mysql): " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n\n";

$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'IlvTDnpJyMitmGnTMJjHRTVjCWFRAxFG';
$db   = getenv('MYSQLDATABASE') ?: 'sshplus';
$port = getenv('MYSQLPORT') ?: '3306';

echo "Host: $host\nUser: $user\nDB: $db\nPort: $port\n\n";

$conn = @new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo "MySQL Connection FAILED: " . $conn->connect_error . "\n";
} else {
    echo "MySQL Connection: OK\n";
    $conn->set_charset("utf8mb4");
    $result = $conn->query("SELECT COUNT(*) as total FROM admin");
    $row = $result->fetch_assoc();
    echo "Admin users: " . $row['total'] . "\n";
}
