# Deployment Guide

## ☁️ Pre-Requisites

Before deploying FluxBB, ensure your server meets these requirements:

1.  **Web Server**: Apache, Nginx, or IIS.
2.  **PHP 7.2+**:
    *   `php-mbstring` (Required for UTF-8).
    *   `php-json` (Required).
    *   `php-xml` (Required).
    *   `php-mysql` / `php-pgsql` / `php-sqlite3` (Database driver).
    *   `php-gd` (Optional, for avatar resizing).
3.  **Database**:
    *   MySQL 5.5+ / MariaDB
    *   PostgreSQL 7.0+
    *   SQLite 3

---

## 📦 Installation Steps

### 1. File Upload
Upload all files from the repository to your web root (e.g., `/var/www/html/forum`).

### 2. Permissions
The web server user (`www-data`, `apache`, `nginx`) needs **write access** to:
*   `cache/` (Recursive)
*   `img/avatars/`

```bash
chown -R www-data:www-data cache/ img/avatars/
chmod -R 755 cache/ img/avatars/
```

### 3. Database Creation
Create an empty database.

**MySQL Example:**
```sql
CREATE DATABASE fluxbb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'flux_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON fluxbb.* TO 'flux_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Run Installer
1.  Open your browser and navigate to `http://your-site.com/install.php`.
2.  Fill in the database details configured in Step 3.
3.  Create the Administrator account.
4.  Click **Start Install**.

### 5. Post-Install Cleanup (CRITICAL)
Once installation is complete, you will be presented with the generated configuration. The script attempts to write `include/config.php`.

**IMPORTANT:** Delete `install.php` immediately.
```bash
rm install.php
```

---

## 🌐 Web Server Configuration

### Apache
FluxBB comes with `.htaccess` files in sensitive directories (`cache/`, `include/`, etc.) to prevent direct access. Ensure `AllowOverride All` is enabled in your Apache config.

### Nginx
Nginx does not read `.htaccess`. You must add these rules to your server block:

```nginx
server {
    listen 80;
    server_name forum.example.com;
    root /var/www/html/forum;
    index index.php;

    # Deny access to internal directories
    location ^~ /cache/ { deny all; return 403; }
    location ^~ /include/ { deny all; return 403; }
    location ^~ /lang/ { deny all; return 403; }
    location ^~ /plugins/ { deny all; return 403; }
    location ^~ /addons/ { deny all; return 403; }

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock; # Adjust version
    }
}
```

---

## 🚀 Production Tuning

1.  **Disable Debugging:**
    Ensure `include/config.php` (if manually modified) or `include/common.php` does not have `E_ALL` visible to users. The default `common.php` handles this correctly (`ini_set('display_errors', 0)`).

2.  **Cache Warming:**
    The system generates cache files on the first request. No manual warming is needed.

3.  **File Permissions:**
    Lock down write permissions on all directories *except* `cache/` and `img/avatars/`.
