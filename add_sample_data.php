<?php
// Add sample customer data
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'inventory_system';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Adding sample customer data...\n";
    
    // Sample data
    $customers = [
        ['John Smith', 'LPG Cylinder 5kg', 'LPG', 2, 15.50, '2026-05-10 10:30:00', 'Regular customer'],
        ['Maria Garcia', 'LPG Cylinder 11kg', 'LPG', 1, 27.50, '2026-05-10 14:15:00', 'First time buyer'],
        ['Robert Johnson', 'LPG Cylinder 2.7kg', 'LPG', 3, 10.50, '2026-05-11 09:00:00', 'Bulk purchase'],
        ['Emily Chen', 'LPG Cylinder 22kg', 'LPG', 1, 48.00, '2026-05-11 11:45:00', 'Commercial use'],
        ['David Wilson', 'LPG Cylinder 7kg', 'LPG', 2, 19.00, '2026-05-11 16:20:00', 'Residential customer']
    ];
    
    foreach ($customers as $customer) {
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, product_bought, product_category, quantity, price, purchase_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidss", $customer[0], $customer[1], $customer[2], $customer[3], $customer[4], $customer[5], $customer[6]);
        $stmt->execute();
        echo "Added: " . $customer[0] . " - " . $customer[1] . "\n";
    }
    
    echo "\nSample data added successfully!\n";
    
    // Verify
    $result = $conn->query("SELECT COUNT(*) as count FROM customers");
    $row = $result->fetch_assoc();
    echo "Total customer records: " . $row['count'] . "\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
