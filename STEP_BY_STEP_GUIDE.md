# Farm-Direct PHP Application - Step-by-Step User Guide

## 📋 Table of Contents
1. [Installation & Setup](#installation--setup)
2. [First Time Access](#first-time-access)
3. [Buyer Journey](#buyer-journey-step-by-step)
4. [Seller Journey](#seller-journey-step-by-step)
5. [Admin Journey](#admin-journey-step-by-step)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Installation & Setup

### Step 1: Install Prerequisites
Ensure you have the following installed:
- **PHP 7.4 or higher** (Check: `php -v`)
- **MySQL 5.7 or higher** (Check: `mysql --version`)
- **Web server** (Apache/Nginx or use PHP built-in server)

### Step 2: Clone the Repository
```bash
git clone https://github.com/April6natu/Farm-Direct.git
cd Farm-Direct
```

### Step 3: Set Up the Database
```bash
# Login to MySQL
mysql -u root -p

# Create the database
CREATE DATABASE agriecom;
exit;

# Import the schema and sample data
mysql -u root -p agriecom < agriecom.sql
```

### Step 4: Configure Database Connection
Open `db.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');     // Your MySQL host
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', 'your_password'); // Your MySQL password
define('DB_NAME', 'agriecom');      // Database name
```

### Step 5: Set File Permissions
```bash
# Make uploads directory writable
chmod 755 uploads/
```

### Step 6: Start the Server
```bash
# Option 1: PHP Built-in Server (for development)
php -S localhost:8000

# Option 2: Configure Apache Virtual Host
# (Configure your virtual host to point to the project directory)
```

### Step 7: Access the Application
Open your browser and navigate to:
- PHP Server: `http://localhost:8000`
- Apache: `http://localhost/Farm-Direct` (or your configured virtual host)

---

## 🎯 First Time Access

### What You'll See
The landing page (`index.php`) displays:
- **Farm-Direct logo and branding**
- **Call-to-action buttons**: "Get Started" and "Login"
- **Quick access options**: Browse Products, Register as Buyer, Register as Seller
- **How It Works section**

### Demo Accounts (Pre-configured)
You can use these accounts to test the system:

| Role   | Email                     | Password    |
|--------|---------------------------|-------------|
| Admin  | admin@farm-direct.com    | admin123    |
| Seller | john@farm-direct.com     | seller123   |
| Buyer  | buyer@test.com           | buyer123    |

---

## 🛒 Buyer Journey (Step-by-Step)

### Step 1: Register as a Buyer

1. **Navigate to Register**
   - Click "Get Started" or "Register" on the landing page
   - URL: `http://localhost:8000/register.php`

2. **Fill Registration Form**
   - **Full Name**: Enter your name (e.g., "Jane Doe")
   - **Email Address**: Enter valid email (e.g., "jane@example.com")
   - **Phone Number**: Enter phone (optional)
   - **I want to**: Select "Buy Products"
   - **Password**: Enter password (minimum 6 characters)
   - **Confirm Password**: Re-enter the same password

3. **Submit**
   - Click "Register" button
   - Success message will appear
   - Click "Login" link to proceed

### Step 2: Login

1. **Navigate to Login Page**
   - URL: `http://localhost:8000/login.php`

2. **Enter Credentials**
   - **Email**: Your registered email or use `buyer@test.com`
   - **Password**: Your password or use `buyer123`

3. **Submit**
   - Click "Login" button
   - You'll be redirected to the product browsing page

### Step 3: Browse Products

**Location**: `browse.php`

1. **View All Products**
   - The page displays all available agricultural products
   - Each product card shows:
     - Product image
     - Product name
     - Category badge
     - Price per unit
     - Short description
     - Stock availability
     - "View Details" and "Add to Cart" buttons

2. **Filter by Category**
   - Click on category pills at the top:
     - All
     - Tubers
     - Grains
     - Legumes
     - Vegetables
     - Fruits/Staples

3. **Search Products**
   - Use the search box in the top right
   - Enter product name or keywords
   - Click "Search" button

### Step 4: View Product Details

1. **Click on Product**
   - Click "View Details" button on any product card
   - URL: `product.php?id=X`

2. **Product Details Page Shows**
   - Large product image
   - Full description
   - Price and unit
   - Stock availability
   - Seller information
   - Related products from same category

3. **Add to Cart**
   - Click "Add to Cart" button
   - Toast notification appears confirming addition
   - Cart badge in navigation updates with item count

### Step 5: Manage Shopping Cart

**Location**: `cart.php`

1. **View Cart**
   - Click cart icon in navigation (shows item count badge)
   - URL: `http://localhost:8000/cart.php`

2. **Cart Page Displays**
   - List of all cart items with:
     - Product image
     - Product name and category
     - Price per unit
     - Quantity input (editable)
     - Item total
     - Remove button
   - Order summary on the right showing total

3. **Update Quantities**
   - Change the number in quantity input
   - Cart automatically updates via AJAX
   - Item total recalculates instantly

4. **Remove Items**
   - Click "Remove" button on any item
   - Confirmation prompt appears
   - Item is removed with smooth animation

### Step 6: Checkout

**Still on**: `cart.php`

1. **Fill Checkout Form** (on the right sidebar)
   - **Delivery Location**: Select from dropdown
     - Central Business District
     - North Industrial Park
     - Riverside Estates
     - Lakeside Heights
     - Market Square Area
     - Green Valley Farm Zone
   
   - **Payment Method**: Choose one
     - Mobile Money (radio button)
     - Credit Card (radio button)

2. **Complete Purchase**
   - Click "Complete Purchase" button
   - System processes order:
     - Creates order record
     - Reduces product stock
     - Sends notifications to sellers
     - Clears your cart

3. **Order Confirmation**
   - Redirected to dashboard with success message
   - Order details saved in your history

### Step 7: View Order History

**Location**: `dashboard.php`

1. **Buyer Dashboard Shows**
   - **Statistics Cards**:
     - Total Orders
     - Pending Orders
     - Delivered Orders
     - Total Spent

2. **Order History Table**
   - Order ID
   - Date placed
   - Number of items
   - Total amount
   - Delivery location
   - Status badge (Pending/Paid/Delivered)
   - "View Details" button

3. **View Order Details**
   - Click "View Details" on any order
   - Modal popup shows:
     - Order information
     - List of items purchased
     - Quantities and prices
     - Order total

---

## 🏪 Seller Journey (Step-by-Step)

### Step 1: Register as a Seller

1. **Navigate to Register**
   - Click "Register" or "Sell Products" button
   - URL: `http://localhost:8000/register.php`

2. **Fill Registration Form**
   - **Full Name**: Enter your name (e.g., "John Farmer")
   - **Email Address**: Enter valid email
   - **Phone Number**: Enter phone (recommended for sellers)
   - **I want to**: Select "Sell Products"
   - **Password**: Enter password (minimum 6 characters)
   - **Confirm Password**: Re-enter password

3. **Submit**
   - Click "Register"
   - Success message appears
   - Proceed to login

### Step 2: Login as Seller

1. **Login Page**
   - Email: Your email or `john@farm-direct.com`
   - Password: Your password or `seller123`
   - Click "Login"

2. **Redirect**
   - Automatically redirected to `seller/dashboard.php`

### Step 3: Seller Dashboard Overview

**Location**: `seller/dashboard.php`

1. **Statistics Cards Display**
   - **Total Products**: Number of your listed products
   - **Total Stock**: Sum of all your product quantities
   - **Total Sales**: Number of sales made
   - **Revenue**: Total earnings from sales

2. **Recent Sales Table**
   - Shows last 10 sales
   - Columns: Product, Buyer, Quantity, Amount, Date, Status

3. **Low Stock Alerts** (if applicable)
   - Warning card shows products with stock < 10
   - Quick link to update stock

4. **Notifications Panel** (right side)
   - Real-time sale notifications
   - Unread count badge
   - Click notification to mark as read
   - Shows: Sale details, time ago

### Step 4: Add New Product

1. **Navigate to Add Product**
   - Click "Add New Product" button on dashboard
   - Or click navigation: Dashboard → My Products → Add Product
   - URL: `seller/add_product.php`

2. **Fill Product Form**
   - **Product Name**: E.g., "Fresh Cassava Tubers"
   - **Category**: Select from dropdown
     - Tubers
     - Grains
     - Legumes
     - Vegetables
     - Fruits/Staples
     - Other
   - **Price**: Enter numeric value (e.g., 15.50)
   - **Unit**: E.g., "kg", "Bunch", "Bag"
   - **Stock Quantity**: Number of units available (e.g., 100)
   - **Description**: Detailed product description
   - **Product Image**: Click "Choose File" and upload image
     - Supported: JPG, PNG, GIF, WebP
     - Max size: 5MB

3. **Submit**
   - Click "Add Product" button
   - Success message appears
   - Redirected to products list after 2 seconds

### Step 5: Manage Products

**Location**: `seller/products.php`

1. **Products Table Shows**
   - Product image thumbnail
   - Product name and unit
   - Category
   - Price
   - Stock (with inline edit)
   - Status badge (Active/Inactive)
   - Action buttons (Edit, Delete)

2. **Update Stock** (Inline)
   - Change number in stock input field
   - Click "Update" button
   - Stock updates immediately

3. **Toggle Product Status**
   - Click status badge (Active/Inactive)
   - Product switches between active and inactive
   - Inactive products won't show to buyers

4. **Edit Product**
   - Click "Edit" button
   - Redirected to `add_product.php?edit=X` with pre-filled form
   - Make changes
   - Click "Update Product"

5. **Delete Product**
   - Click "Delete" button
   - Confirmation prompt appears
   - Product permanently removed from system

### Step 6: Monitor Sales

1. **Return to Dashboard**
   - Click "Dashboard" in navigation

2. **Check Recent Sales**
   - View recent transactions
   - See buyer information
   - Track order status

3. **Check Notifications**
   - New sale notifications appear instantly
   - Unread notifications highlighted in green
   - Click to mark as read

---

## 👨‍💼 Admin Journey (Step-by-Step)

### Step 1: Login as Admin

1. **Login Page**
   - Email: `admin@farm-direct.com`
   - Password: `admin123`
   - Click "Login"

2. **Redirect**
   - Automatically redirected to `admin/dashboard.php`

### Step 2: Admin Dashboard Overview

**Location**: `admin/dashboard.php`

1. **System Statistics** (Top Cards)
   - **Buyers**: Total buyer accounts
   - **Sellers**: Total seller accounts
   - **Active Products**: Products currently available
   - **Orders**: Total orders placed
   - **Revenue**: Total system revenue

2. **Tabbed Interface**
   - **Users Management** (default active)
   - **Products Management**
   - **Orders Management**

### Step 3: Manage Users

**Tab**: Users Management

1. **User Directory Table Shows**
   - User ID
   - Name
   - Email
   - Role (dropdown - editable)
   - Join date
   - Delete button

2. **Add New User**
   - Click "+ Add User" button
   - Modal popup appears
   - Fill form:
     - Name
     - Email
     - Password
     - Role (Buyer/Seller/Admin)
   - Click "Add User"
   - User created immediately

3. **Change User Role**
   - Click role dropdown on any user
   - Select new role (Buyer/Seller/Admin)
   - Dropdown automatically submits
   - Role updates instantly

4. **Delete User**
   - Click "Delete" button on any user (except yourself)
   - Confirmation prompt appears
   - User and all related data removed

### Step 4: Manage Products

**Tab**: Products Management

1. **Products Overview Table Shows**
   - Product ID
   - Product name
   - Seller name
   - Category
   - Price
   - Stock level
   - Status (dropdown - editable)
   - Delete button

2. **Update Product Status**
   - Click status dropdown (Active/Inactive)
   - Select new status
   - Dropdown automatically submits
   - Status updates instantly

3. **Delete Product**
   - Click "Delete" button
   - Confirmation prompt
   - Product permanently removed

### Step 5: Manage Orders

**Tab**: Orders Management

1. **Orders Table Shows**
   - Order ID
   - Buyer name
   - Total amount
   - Payment method
   - Delivery location
   - Order date
   - Status (dropdown - editable)

2. **Update Order Status**
   - Click status dropdown on any order
   - Select new status:
     - Pending
     - Paid
     - Delivered
     - Cancelled
   - Dropdown automatically submits
   - Status updates instantly

### Step 6: Monitor System Health

1. **Review Statistics**
   - Check total users, products, orders
   - Monitor revenue trends

2. **Check for Issues**
   - Inactive products
   - Pending orders
   - User account problems

---

## 🔧 Troubleshooting

### Problem: Cannot connect to database

**Solution:**
1. Check MySQL is running: `sudo systemctl status mysql`
2. Verify credentials in `db.php`
3. Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`
4. Re-import schema: `mysql -u root -p agriecom < agriecom.sql`

### Problem: "Permission denied" on uploads

**Solution:**
```bash
chmod 755 uploads/
# If using Apache
sudo chown www-data:www-data uploads/
```

### Problem: Images not uploading

**Solution:**
1. Check `php.ini` settings:
   - `upload_max_filesize = 5M`
   - `post_max_size = 5M`
2. Restart PHP/Apache after changes
3. Verify uploads directory exists and is writable

### Problem: Cart not updating (AJAX not working)

**Solution:**
1. Check browser console for JavaScript errors (F12)
2. Verify jQuery is loading (check network tab)
3. Check `actions_cart_action.php` is accessible
4. Clear browser cache

### Problem: Session timeout/logged out unexpectedly

**Solution:**
1. Check `php.ini` session settings:
   - `session.gc_maxlifetime = 1440`
2. Ensure session directory is writable
3. Re-login to create new session

### Problem: Products not showing

**Solution:**
1. Check if products exist: `SELECT * FROM products WHERE status='active';`
2. Verify you're logged in as buyer
3. Check category filter isn't hiding products
4. Clear search query

### Problem: Notifications not appearing

**Solution:**
1. Check notifications table: `SELECT * FROM notifications;`
2. Ensure sellers are registered correctly
3. Place a test order to trigger notification
4. Refresh seller dashboard

### Problem: Password reset not working

**Note:** Password reset feature not implemented yet.
**Workaround:** Update password directly in database:
```sql
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'user@example.com';
-- New password will be: password
```

---

## 📞 Additional Support

### File Structure Reference
```
Farm-Direct/
├── index.php           # Landing page
├── login.php           # Authentication
├── register.php        # User registration
├── browse.php          # Product catalog
├── cart.php            # Shopping cart
├── dashboard.php       # Buyer orders
├── seller/
│   ├── dashboard.php   # Seller stats
│   ├── products.php    # Manage products
│   └── add_product.php # Add/edit products
└── admin/
    └── dashboard.php   # Admin panel
```

### Database Tables
- `users` - All user accounts
- `products` - Product listings
- `cart` - Shopping cart items
- `orders` - Purchase orders
- `order_items` - Items in orders
- `notifications` - Seller alerts

### Security Notes
- All passwords are hashed with bcrypt
- SQL injection prevented with prepared statements
- XSS prevented with output sanitization
- File uploads validated for type and size
- Sessions secured with proper configuration

---

**Farm-Direct** 🌾 - Happy farming and trading!
