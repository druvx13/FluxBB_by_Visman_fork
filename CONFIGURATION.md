# Configuration Reference

## ⚙️ `include/config.php`
This file is generated during installation. It contains the credentials required to connect to the database. It is **not** meant to be edited manually unless the database moves.

| Variable | Description | Example |
|----------|-------------|---------|
| `$db_type` | Database driver to use. | `'mysql'`, `'mysqli'`, `'pgsql'`, `'sqlite3'` |
| `$db_host` | Hostname of the database server. | `'localhost'`, `'127.0.0.1'` |
| `$db_name` | Name of the database. | `'fluxbb'` |
| `$db_username` | Database user. | `'flux_user'` |
| `$db_password` | Database password. | `'secret'` |
| `$db_prefix` | Table prefix. | `'pun_'` |
| `$p_connect` | Use persistent connections. | `false` |
| `$cookie_name` | Name of the auth cookie. | `'pun_cookie_123'` |
| `$cookie_domain`| Domain scope for the cookie. | `''` (current domain) |
| `$cookie_path` | Path scope for the cookie. | `'/'` |
| `$cookie_secure`| HTTPS only cookie. | `0` or `1` |
| `$cookie_seed` | Random string for hash generation. | `'a8f7...'` |

---

## ⚙️ Global Configuration (`$pun_config`)
These settings are managed via the **Admin Control Panel** -> **Options**. They are stored in the `config` table and cached in `cache/cache_config.php`.

### General
*   `o_board_title`: The title of the forum.
*   `o_board_desc`: The description/tagline.
*   `o_default_timezone`: Server timezone offset.
*   `o_default_lang`: Default language (e.g., 'English').

### Features
*   `o_censoring`: Enable word censoring (1/0).
*   `o_quickpost`: Enable Quick Reply box (1/0).
*   `o_users_online`: Show "Users Online" box (1/0).
*   `o_signatures`: Allow signatures (1/0).
*   `o_search_all_forums`: Allow searching globally (1/0).

### Registration
*   `o_regs_allow`: Allow new registrations (1/0).
*   `o_regs_verify`: Require email verification (1/0).
*   `o_regs_report`: Email admin on new registration (1/0).

### Posting
*   `o_smilies`: Convert text to smilies (1/0).
*   `o_smilies_sig`: Allow smilies in signatures (1/0).
*   `o_make_links`: Auto-detect URLs (1/0).

### Maintenance
*   `o_maintenance`: Enable maintenance mode (1/0).
*   `o_maintenance_message`: Message displayed during maintenance.

---

## ⚙️ Environment Variables
This application typically **does not** use OS-level environment variables (like `.env` files) natively. All configuration is done via `config.php` or the database.

However, the **PHP Environment** settings are critical:
*   `upload_max_filesize`: Controls avatar/attachment limits.
*   `post_max_size`: Controls maximum form submission size.
*   `memory_limit`: Script execution memory cap.
