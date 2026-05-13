# Customer Records Setup Instructions

## Database Setup

To set up the customer records functionality, you need to create the customers table in your database.

### Method 1: Using SQL File
Run the SQL file `create_customers_table.sql` in your MySQL database:

```sql
-- You can run this command in your MySQL client:
mysql -u username -p inventory_system < create_customers_table.sql
```

### Method 2: Manual SQL Execution
Execute the following SQL commands in your database:

```sql
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    product_bought VARCHAR(255) NOT NULL,
    product_category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Features Implemented

1. **Customer Records Tab**: Added to navigation menu
2. **Customer CRUD Operations**: Create, Read, Update, Delete customer records
3. **Customer Search**: Search customers by name, product, or category
4. **Customer Summary**: Top customers and popular categories
5. **Sales Integration**: View customer sales overview
6. **New Sale Button**: Moved from sales page to customer records page
7. **Add Customer Button**: Added to customer records page

## File Structure Created

- `database/create_customers_table.sql` - Database table creation
- `app/Models/CustomerModel.php` - Customer data model
- `app/Controllers/Customer.php` - Customer controller
- `app/Views/customer/index.php` - Customer listing page
- `app/Views/customer/create.php` - Add customer form
- `app/Views/customer/edit.php` - Edit customer form
- `app/Views/customer/sales.php` - Customer sales overview

## Routes Added

- `/customer` - Customer records listing
- `/customer/create` - Add new customer
- `/customer/edit/{id}` - Edit customer
- `/customer/view-sales/{customer_name}` - View customer sales
- `/customer/create-sale` - Create new sale (redirects to sales)
- `/customer/delete/{id}` - Delete customer record

## Usage

1. Set up the database table using the SQL commands above
2. Navigate to `/customer` to access customer records
3. Use the "Add Customer" button to create new customer records
4. Use the "+ New Sale" button to create sales from customer records
5. View customer sales by clicking "View Sales" on any customer record
