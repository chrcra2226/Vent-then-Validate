# Vent then Validate
### Customer Complaint Management System
Advanced Server-Side Scripting with PHP — Course Project

---

## Project Overview

Vent Then Validate is a PHP and MySQL web application that allows customers to submit and track complaints while enabling administrators to manage, update, and resolve those complaints. The system is built using MVC architecture with PDO for secure database access and role-based authentication.

---

## Features

### Customer Features
- Register and login securely
- Submit complaints with category, title, and description
- Attach image files (JPG, PNG, GIF) or PDF documents to complaints
- View all submitted complaints with real-time status badges
- View full complaint details including status history and attached files
- Preview attached images inline on the complaint detail page

### Administrator Features
- View all complaints from all customers
- Filter complaints by status (Open, In Review, Resolved, Closed)
- Manage individual complaints — update status and add notes
- View full status history audit trail for every complaint
- Dashboard with complaint statistics by status
- Download files attached to complaints securely

### Security Features
- Password hashing using bcrypt via password_hash()
- PDO prepared statements on all database queries
- CSRF token protection on all POST forms
- Session management with 30 minute timeout
- Role-based access control (Customer / Administrator)
- XSS prevention with htmlspecialchars() on all output
- .htaccess files blocking direct access to source files
- Secure file upload with MIME type validation
- Secure file download handler verifying user authorization

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| Server-Side Language | PHP 8.2 |
| Database | MySQL 8.x |
| Database API | PDO with Prepared Statements |
| Front-End | HTML5, CSS3, JavaScript |
| Local Environment | XAMPP (Apache + MySQL) |
| Version Control | Git / GitHub |

---

## Project Structure
vent-then-validate/

├── config/

│   └── database.php          # Database credentials

├── public/                   # Web root — entry points only

│   ├── css/

│   │   └── style.css

│   ├── admin/

│   │   ├── main_dashboard.php

│   │   ├── main_complaints.php

│   │   └── main_manage-complaint.php

│   ├── index.php

│   ├── main_login.php

│   ├── main_register.php

│   ├── main_my-complaint.php

│   ├── main_submit-complaint.php

│   ├── main_complaint-detail.php

│   ├── download-file.php

│   ├── logout.php

│   └── .htaccess

├── src/

│   ├── controllers/

│   │   ├── UserController.php

│   │   └── ComplaintController.php

│   ├── models/

│   │   ├── Model.php

│   │   ├── User.php

│   │   ├── Category.php

│   │   ├── Complaint.php

│   │   ├── ComplaintFile.php

│   │   └── StatusHistory.php

│   ├── views/

│   │   ├── layouts/

│   │   │   ├── header.php

│   │   │   ├── navbar.php

│   │   │   └── footer.php

│   │   ├── customer/

│   │   │   ├── home.php

│   │   │   ├── login.php

│   │   │   ├── register.php

│   │   │   ├── my-complaints.php

│   │   │   ├── submit-complaint.php

│   │   │   └── complaint-detail.php

│   │   └── admin/

│   │       ├── dashboard.php

│   │       ├── complaints.php

│   │       └── manage-complaint.php

│   ├── util/

│   │   ├── validation.php

│   │   └── security.php

│   └── Database.php

├── uploads/

│   └── .htaccess

└── .htaccess

---

## Setup Instructions

### Prerequisites
- XAMPP (Apache + MySQL + PHP 8.x)
- Git
- Web browser (Google Chrome recommended)

### Installation Steps

**1. Clone the repository**
```bash
git clone https://github.com/chrcra2226/vent-then-validate.git
```

**2. Move to XAMPP htdocs**

Copy or clone the project into:
C:\xampp\htdocs\vent-then-validate\

**3. Start XAMPP**

Open XAMPP Control Panel and start both Apache and MySQL.

**4. Create the database**

Open phpMyAdmin at `http://localhost/phpmyadmin` and run:
```sql
CREATE DATABASE vent_then_validate;
USE vent_then_validate;
```

Then run the SQL scripts to create all tables in this order:
1. users
2. categories
3. complaints
4. complaint_files
5. status_history

Refer to the database schema section below for the full SQL.

**5. Configure database credentials**

Open `/config/database.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vent_then_validate');
```

**6. Create the uploads folder**

Make sure the `/uploads/` folder exists at the project root and is writable.

**7. Access the application**

Open your browser and navigate to:
http://localhost/vent-then-validate/public/index.php

---

## Database Schema

### users
```sql
CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
);
```

### categories
```sql
CREATE TABLE categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    PRIMARY KEY (category_id)
);
```

### complaints
```sql
CREATE TABLE complaints (
    complaint_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    category_id INT(11) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open', 'In Review', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (complaint_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
);
```

### complaint_files
```sql
CREATE TABLE complaint_files (
    file_id INT(11) NOT NULL AUTO_INCREMENT,
    complaint_id INT(11) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (file_id),
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE
);
```

### status_history
```sql
CREATE TABLE status_history (
    history_id INT(11) NOT NULL AUTO_INCREMENT,
    complaint_id INT(11) NOT NULL,
    old_status ENUM('Open', 'In Review', 'Resolved', 'Closed'),
    new_status ENUM('Open', 'In Review', 'Resolved', 'Closed') NOT NULL,
    changed_by INT(11) NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    PRIMARY KEY (history_id),
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(user_id) ON DELETE RESTRICT
);
```

### Seed Categories
```sql
INSERT INTO categories (name, description) VALUES
('Billing', 'Issues related to charges, invoices, or payment processing'),
('Customer Service', 'Issues related to staff conduct or quality of service received'),
('Product Quality', 'Issues related to defective or unsatisfactory products'),
('Delivery', 'Issues related to shipping, timing, or damaged deliveries'),
('Technical Support', 'Issues related to software, hardware, or technical assistance');
```

---

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@ventvalidate.com | Admin123 |
| Customer | customer@ventvalidate.com | Customer123 |

---

## Weekly Development Summary

| Week | Focus | Key Deliverables |
|------|-------|-----------------|
| Week 1 | Planning | Project plan, database schema, wireframes |
| Week 2 | Framework | Database, folder structure, core pages, forms |
| Week 3 | Models & Controllers | PHP classes, CRUD operations, dynamic pages |
| Week 4 | Security & MVC | Authentication, CSRF, RBAC, MVC restructure |
| Week 5 | File Support & Polish | File upload, UI polish, testing, documentation |

---

## Author

**Christopher Crayton**
Advanced Server-Side Scripting with PHP
2026
