# smart_gadget_store_final

A PHP‑based e‑commerce platform for selling smart gadgets, featuring a full‑featured admin dashboard, shopping cart, and service‑provider appointment system.

---

## Overview

`smart_gadget_store_final` is a lightweight, modular web application that allows customers to browse products, add items to a cart, and place orders. Administrators can manage categories, products, orders, reviews, and service‑provider appointments through a dedicated backend.

---

## Features

- **Customer‑Facing**
  - Product catalog with category browsing
  - Shopping cart (`add_to_cart.php`, `cart.php`)
  - Secure checkout and payment handling (`charge.php`)
  - User authentication (`login.php`, `logout.php`)
  - Service‑provider appointment scheduling (`make_appointment.php`)

- **Admin Dashboard**
  - Category & product management (`admin/add_categories.php`, `admin/edit_product.php`, etc.)
  - Order tracking and item details (`admin/admin_orders.php`, `admin/admin_order_items.php`)
  - Review moderation (`admin/admin_reviews.php`, `admin/admin_provider_reviews.php`)
  - Service‑provider management (`admin/admin_service_providers.php`, `admin/admin_provider_appointments.php`)
  - Secure admin login (`admin/admin_login.php`) and session handling

- **Technical**
  - Clean separation of concerns (config files, CSS, SQL schema)
  - Ready‑to‑run SQL dump (`Database/smartstore_db.sql`)
  - Comprehensive documentation (`Project file.docx`)

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 7.x+ |
| Database | MySQL / MariaDB |
| Front‑end | HTML5, CSS3 (`css/style.css`) |
| Server | Apache / Nginx (compatible with any LAMP stack) |
| Version Control | Git |

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/smart_gadget_store_final.git
   cd smart_gadget_store_final
   ```

2. **Create the database**

   ```sql
   -- In MySQL client or phpMyAdmin
   SOURCE Database/smartstore_db.sql;
   ```

3. **Configure database connection**

   - Copy `config.php.example` to `config.php` (if provided) or edit the existing `config.php` and `admin/config.php`.
   - Replace placeholder values with your own credentials:

     ```php
     define('DB_HOST', 'YOUR_DB_HOST');
     define('DB_NAME', 'YOUR_DB_NAME');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

4. **Set up a local web server**

   - Place the project folder inside your web root (e.g., `htdocs` for XAMPP).
   - Ensure PHP is enabled and the `mod_rewrite` module (if using Apache) is active.

5. **Adjust file permissions** (if required)

   ```bash
   chmod -R 755 .
   ```

6. **Open the site**

   - Customer view: `http://localhost/smart_gadget_store_final/index.php`
   - Admin panel: `http://localhost/smart_gadget_store_final/admin/admin_login.php`

---

## Usage

### Customer Flow
1. Visit `index.php` to browse gadgets.
2. Click **Add to Cart** on a product (handled by `add_to_cart.php`).
3. Review cart contents via `cart.php`.
4. Proceed to checkout (`charge.php`) and complete the order.

### Admin