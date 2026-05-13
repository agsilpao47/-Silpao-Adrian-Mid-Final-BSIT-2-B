<?php
// Database connection check
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'inventory_system';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Database connection: SUCCESS\n\n";
    
    // Check if customers table exists
    $result = $conn->query("SHOW TABLES LIKE 'customers'");
    
    if ($result->num_rows > 0) {
        echo "✓ Customers table exists\n";
        
        // Show table structure
        echo "\nTable structure:\n";
        $result = $conn->query("DESCRIBE customers");
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
        
        // Show record count
        $result = $conn->query("SELECT COUNT(*) as count FROM customers");
        $row = $result->fetch_assoc();
        echo "\nRecord count: " . $row['count'] . "\n";
        
        // Show sample data if exists
        if ($row['count'] > 0) {
            echo "\nSample records:\n";
            $result = $conn->query("SELECT * FROM customers LIMIT 3");
            while ($row = $result->fetch_assoc()) {
                echo "ID: " . $row['id'] . ", Name: " . $row['customer_name'] . ", Product: " . $row['product_bought'] . "\n";
            }
        }
        
    } else {
        echo "✗ Customers table does NOT exist\n";
        echo "\nTo create it, run this SQL:\n";
        echo file_get_contents('database/create_customers_table.sql');
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
