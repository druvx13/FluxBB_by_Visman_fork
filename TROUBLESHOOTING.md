# Troubleshooting Guide

## 🛑 Common Issues

### 1. "Unable to write configuration cache file"
*   **Symptom:** White screen or error message mentioning `cache/cache_config.php`.
*   **Cause:** The `cache/` directory is not writable by the web server.
*   **Fix:**
    ```bash
    chmod 777 cache/
    # OR better
    chown www-data:www-data cache/
    ```

### 2. "The constant PUN_ROOT must be defined"
*   **Symptom:** You are trying to access an include file directly (e.g., `include/common.php`).
*   **Fix:** Only access root files (`index.php`, `viewtopic.php`). Do not call library files directly.

### 3. Blank Page (White Screen of Death)
*   **Cause:** PHP Fatal Error with `display_errors` disabled.
*   **Fix:**
    1.  Check your web server error logs (`/var/log/nginx/error.log` or `/var/log/apache2/error.log`).
    2.  Temporarily enable error display in `include/common.php` (Line ~12):
        ```php
        ini_set('display_errors', 1);
        ```
    3.  **Revert this change immediately after debugging.**

### 4. Database Connection Errors
*   **Symptom:** "Unable to connect to database."
*   **Fix:**
    1.  Check `include/config.php`.
    2.  Verify the database server is running.
    3.  Verify the username/password are correct.
    4.  If using `localhost`, try `127.0.0.1` (socket vs TCP issue).

### 5. "Bad Request" or "CSRF token mismatch"
*   **Cause:** Your session expired or you clicked an invalid link.
*   **Fix:** Log out and log back in. Clear browser cookies.

---

## 🔧 Maintenance Tasks

### Rebuilding Caches
If you manually edited the database and changes aren't showing:
1.  Delete all files in `cache/` except `.htaccess` and `index.html`.
2.  Reload any page on the forum. The system will regenerate them.

### Database Repair
If tables are corrupted (MySQL MyISAM):
1.  Run `REPAIR TABLE pun_posts;` (etc) in your database manager (phpMyAdmin).
2.  FluxBB does not include a built-in repair tool in the frontend.

### Moving the Forum
1.  **Backup:** Dump the database and zip the files.
2.  **Move:** Upload files to new server. Import SQL.
3.  **Update Config:** Edit `include/config.php` with new DB credentials.
4.  **Update URL:**
    *   If the domain changed, you must update the `o_base_url` in the `pun_config` table.
    *   `UPDATE pun_config SET conf_value = 'http://new-domain.com' WHERE conf_name = 'o_base_url';`
    *   Delete `cache/cache_config.php`.
