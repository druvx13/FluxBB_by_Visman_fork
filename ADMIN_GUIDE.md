# Administration Guide

## 👑 Accessing the Admin Panel
1.  Log in with an account that belongs to the **Administrators** group.
2.  Click the **Administration** link in the navigation menu (usually top right).

---

## 🔧 Core Functions

### Index
*   **Server Statistics:** View PHP version, database size, and row counts.
*   **Updates:** Check for FluxBB updates (may not apply to this fork automatically).

### Setup
*   **Options:** Configure board title, timezones, email settings, and features.
*   **Maintenance:** Turn the board offline. Admins can still browse.

### Forums & Categories
*   **Categories:** Create containers for forums (e.g., "General", "Tech Support").
*   **Forums:** Create actual discussion boards inside categories.
*   **Permissions:** Set specific access rights for each group per forum (Read, Post, Reply).

### Users & Groups
*   **Users:** Search, ban, or delete users. Change user groups.
*   **Groups:** Define permissions for ranks (e.g., "Moderators", "Members").
*   **Ranks:** Set titles based on post count (e.g., "Newbie", "Veteran").
*   **Bans:** Ban users by Username, IP, or Email.

### Management
*   **Reports:** View and resolve user reports about posts.
*   **Pruning:** Mass delete old topics to save space.

---

## 🧩 Plugins & Extensions
*   Navigate to **Extensions** (if available) or specific plugin tabs.
*   The `plugins/` folder contains drop-in admin modules. To install a new plugin, upload it to this directory and look for it in the Admin Panel sidebar.

---

## 🚑 Emergency Recovery

### Forgot Admin Password?
1.  If email is configured, use the "Forgotten Password" link on the login page.
2.  **Database Method:**
    *   Generate a new hash (depends on hashing algorithm, usually SHA-1 + Salt in older versions, or bcrypt in newer).
    *   Safest: Create a new user, register normally, then update the database:
        ```sql
        UPDATE pun_users SET group_id = 1 WHERE username = 'new_admin';
        ```

### Locked Out (Maintenance Mode)?
1.  Admins can always login.
2.  If stuck, edit `cache/cache_config.php` (Temporary fix, will be overwritten) OR edit the database:
    ```sql
    UPDATE pun_config SET conf_value = '0' WHERE conf_name = 'o_maintenance';
    ```
    Then delete `cache/cache_config.php` to force a rebuild.
