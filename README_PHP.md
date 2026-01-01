# Farm-Direct PHP Application

A complete PHP-based agricultural eCommerce platform connecting farmers directly with buyers.

## Features

### Three User Roles
- **Admin**: Full system access, manage users, products, and orders
- **Seller**: Manage product inventory, receive sale notifications, track revenue
- **Buyer**: Browse products, manage cart, place orders, track deliveries

### Core Functionality
- User authentication and registration
- CRUD operations for all entities (Users, Products, Orders)
- AJAX-powered shopping cart
- Image upload for products
- Real-time notifications for sellers
- Order management and tracking
- Mobile-responsive design
- Search and filter products by category

## Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Styling**: Bootstrap 5.3
- **Architecture**: CRUD methodology with prepared statements

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for clean URLs)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/April6natu/Farm-Direct.git
   cd Farm-Direct
   ```

2. **Configure Database**
   - Create a new MySQL database named `agriecom`
   - Import the database schema:
     ```bash
     mysql -u root -p agriecom < agriecom.sql
     ```

3. **Update Database Configuration**
   - Edit `db.php` and update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'agriecom');
     ```

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   ```

5. **Start Server**
   - Using PHP built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Or configure your Apache/Nginx virtual host

6. **Access the Application**
   - Open browser and navigate to: `http://localhost:8000`

## Default Login Credentials

### Admin
- Email: `admin@farm-direct.com`
- Password: `admin123`

### Seller
- Email: `john@farm-direct.com`
- Password: `seller123`

### Buyer
- Email: `buyer@test.com`
- Password: `buyer123`

## Project Structure

```
Farm-Direct/
├── admin/                  # Admin dashboard and management
│   └── dashboard.php
├── seller/                 # Seller dashboard and product management
│   ├── dashboard.php
│   ├── products.php
│   ├── add_product.php
│   └── actions.php
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── includes/               # Reusable components
│   ├── header.php
│   └── footer.php
├── uploads/                # Product images (not in repo)
├── agriecom.sql           # Database schema
├── db.php                 # Database connection
├── functions.php          # Helper functions
├── index.php              # Landing page
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Logout handler
├── browse.php             # Product browsing (READ)
├── product.php            # Product details (READ)
├── cart.php               # Shopping cart (CRUD)
├── dashboard.php          # Buyer dashboard (READ)
├── order_details.php      # Order details (READ)
└── actions_cart_action.php # AJAX cart handler (CRUD)
```

## CRUD Implementation

All database operations follow CRUD methodology:

### CREATE
- New users (registration)
- New products (sellers)
- New orders (checkout)
- Notifications

### READ
- Product listings with filters
- User dashboards
- Order history
- Sales reports
- Notifications

### UPDATE
- Product details and stock
- Order status
- User roles (admin)
- Cart quantities
- Notification read status

### DELETE
- Products
- Cart items
- Users (admin)

## Security Features
- Password hashing with `password_hash()`
- Prepared statements to prevent SQL injection
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- Role-based access control
- File upload validation
- CSRF protection on forms

## API Endpoints (AJAX)

### Cart Actions
- `POST /actions_cart_action.php?action=add` - Add to cart
- `POST /actions_cart_action.php?action=remove` - Remove from cart
- `POST /actions_cart_action.php?action=update` - Update quantity

### Seller Actions
- `POST /seller/actions.php?action=mark_notification_read` - Mark notification as read

## Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Mobile Responsive
- Fully responsive design
- Optimized for tablets and smartphones
- Touch-friendly interface

## Future Enhancements
- Payment gateway integration
- Email notifications
- SMS alerts
- Product reviews and ratings
- Advanced analytics
- Export reports to PDF/Excel
- Multi-language support

## License
MIT License - See LICENSE file for details

## Support
For issues and questions, please open an issue on GitHub.

## Contributors
- Farm-Direct Development Team

---
**Farm-Direct** - Connecting farmers to your table 🌾
