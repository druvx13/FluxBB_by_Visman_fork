# Security Model

## 🛡 Authentication & Session Management

### Cookie-Based Auth
FluxBB uses a cryptographic cookie to identify users.

*   **Cookie Name:** Defined in `config.php` (default: `pun_cookie`).
*   **Structure:** Serialization of User ID and Password Hash (HMAC/Salted).
*   **Validation:**
    *   On every request, `check_cookie()` decrypts the cookie.
    *   It validates the user ID exists.
    *   It compares the stored password hash against the database.
*   **Session Hijacking Protection:**
    *   The cookie can be bound to the IP address (Configurable).

### Password Storage
*   Passwords are **never** stored in plain text.
*   They are hashed (implementation varies by version/extensions, check `include/functions.php` -> `pun_hash`).

---

## 🚦 Authorization & Permissions

FluxBB uses a Group-Based Access Control (GBAC) model.

### Groups
Users belong to exactly one primary group (Table: `groups`).
*   **Administrator (ID 1):** Full access.
*   **Moderator (ID 2):** Access to moderation tools.
*   **Guest (ID 3):** Unauthenticated users.
*   **Member (ID 4):** Standard users.

### Permissions
Permissions are boolean flags stored in the `groups` table.
*   `g_read_board`: Can view the index.
*   `g_view_users`: Can view user list.
*   `g_post_replies`: Can reply to topics.
*   `g_post_topics`: Can create topics.
*   `g_is_guest`: Identification flag.

### Forum-Specific Permissions
Overrides global group permissions per forum (Table: `forum_perms`).
*   Allows private/hidden forums.

---

## 🔒 Input Validation & Sanitization

### The "Clean" Philosophy
FluxBB assumes all input is malicious.

1.  **Global Sanitization:**
    *   `forum_remove_bad_characters()` runs on startup to strip null bytes and invisible control characters from `$_GET`, `$_POST`, `$_COOKIE`.

2.  **HTML Handling:**
    *   **Strict No-HTML Policy:** Users cannot post raw HTML.
    *   **BBCode:** The only allowed markup is BBCode, which is parsed safely into HTML by `include/parser.php`.
    *   **Escaping:** `pun_htmlspecialchars()` is used extensively to convert `< > & "` to HTML entities before display.

3.  **SQL Injection Prevention:**
    *   **Intvals:** IDs are cast to integers (`intval($_GET['id'])`).
    *   **Escaping:** String inputs are escaped using the database driver's native escaping function via `$db->escape()`.

---

## 🛡 CSRF Protection

Cross-Site Request Forgery is mitigated using tokens.

*   **Tokens:** Unique hashes generated for sensitive actions.
*   **Implementation:**
    *   Forms include `<input type="hidden" name="csrf_token" value="..." />`.
    *   Processing scripts verify `$_POST['csrf_token']` matches the user's session token.
    *   `confirm_referrer()` checks the HTTP Referer header as a secondary measure.

---

## 🔍 Audit & Logging

*   **Error Logs:** PHP errors are logged to the server's error log.
*   **Admin Logs:** Currently, FluxBB 1.5 does not have a comprehensive database-driven audit log for all admin actions, though critical errors are reported.
*   **Report System:** Users can report posts, creating entries in the `reports` table for moderators to review.

---

## ⚠️ Known Assumptions/Risks

*   **Writable Directories:** The `cache/` and `img/avatars/` directories must be writable by the web server. Improper server configuration could allow execution of uploaded scripts if `.htaccess` (provided) is ignored or overridden.
*   **Installation File:** `install.php` **MUST** be deleted after installation. Leaving it poses a complete takeover risk.
*   **Session IP Binding:** If IP binding is disabled, cookie theft allows session hijacking.
