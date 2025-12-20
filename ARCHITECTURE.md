# System Architecture

## 🏗 High-Level Architecture

The architecture of this FluxBB fork follows a traditional **Page Controller** pattern common in PHP applications of its era. Each URL corresponds to a specific PHP script in the root directory (e.g., `viewtopic.php`, `post.php`), which handles the request, executes business logic, and renders the response.

### Core Components

1.  **Bootstrap (`include/common.php`)**:
    *   The entry point for almost every script.
    *   Loads configuration (`config.php`).
    *   Initializes the database connection.
    *   Loads user session (`check_cookie`).
    *   Loads language files.
    *   Handles ban checks and maintenance mode.

2.  **Database Layer (`include/dblayer/`)**:
    *   An abstraction layer (`DBLayer` class) normalizes differences between MySQL, PostgreSQL, and SQLite.
    *   Provides methods like `query()`, `fetch_assoc()`, `escape()`.

3.  **Template Engine (`include/template/`)**:
    *   A simple substitution-based template system.
    *   Templates (e.g., `main.tpl`) contain placeholders like `<pun_main>`.
    *   Output buffering is used to capture content and inject it into the template.

4.  **Extension System**:
    *   **Plugins (`plugins/`)**: Standalone scripts that admin modules can link to.
    *   **Addons (`addons/`)**: Hook-based extensions that intervene in core logic (e.g., `security_for_post.php`).

---

## 🔄 Request Lifecycle

1.  **Incoming Request:** Web server routes request to a PHP file (e.g., `viewtopic.php`).
2.  **Initialization:**
    *   `include/common.php` is required.
    *   `$pun_user` is populated (Guest or Authenticated User).
    *   Global configuration `$pun_config` is loaded from cache.
3.  **Input Processing:**
    *   `GET` and `POST` variables are sanitized.
    *   Logic specific to the page is executed (e.g., fetching posts from DB).
4.  **Action/Logic:**
    *   Database queries are executed via `$db` global.
    *   Calculations, permissions checks (`$pun_user['g_id']`).
5.  **Rendering:**
    *   `header.php` is included (starts output buffering, outputs HTML head).
    *   Page content is echoed.
    *   `footer.php` is included (ends output buffering, injects content into `main.tpl`, sends response).

---

## 💾 Data Access Patterns

*   **Global `$db` Object:** Accessible in all scopes.
*   **Direct SQL:** Queries are written directly in the logic files.
*   **Caching:**
    *   Heavy configurations (config, bans, ranks, smilies) are cached to PHP files in `cache/`.
    *   The function `generate_config_cache()` writes these files.
    *   The application reads the cache file (e.g., `cache_config.php`) instead of querying the DB on every request.

---

## 🔐 Authentication & Authorization

### Authentication
*   **Mechanism:** Cookie-based.
*   **Logic:** `check_cookie()` in `include/functions.php`.
*   **Flow:**
    1.  Read `pun_cookie`.
    2.  Decrypt/Validate ID and Password Hash.
    3.  Fetch user profile from `users` table.
    4.  Populate `$pun_user` array.

### Authorization
*   **Group-Based:** Users belong to groups (Admin, Mod, Guest, Member).
*   **Permissions:** stored in `groups` table (e.g., `g_read_board`, `g_post_replies`).
*   **Runtime Check:** `if ($pun_user['g_read_board'] == '0') message($lang_common['No view']);`

---

## 📐 Separation of Concerns

While not an MVC framework, the system maintains separation via:

*   **Logic:** Root PHP files (`viewforum.php`).
*   **Presentation:** `include/template/*.tpl` and CSS files.
*   **Data:** `include/dblayer/`.
*   **Localization:** `lang/` directory isolates text strings.

---

## 🚨 Error Handling

*   **Display:** Controlled by `error_reporting()` in `common.php`.
*   **Mechanism:** `error()` function in `include/functions.php` halts execution and displays a styled error message.
*   **Logging:** PHP `error_log` is utilized.

---

## 🌍 Global State Management

The application relies heavily on global variables:
*   `$pun_config`: Array of site settings.
*   `$pun_user`: Array of current user's data.
*   `$db`: Database connection object.
*   `$lang_common`, `$lang_*`: Localization arrays.
