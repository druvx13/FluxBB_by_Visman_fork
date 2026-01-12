# FluxBB (Visman Fork) - Enhanced for Academic Project

## 🎓 Academic Project Notice

**This repository is being used as a foundation for an academic minor project** at [Your College Name] during the 4th semester (2025-2026). The project involves substantial security enhancements, performance optimizations, and feature additions to the existing FluxBB forum platform.

**Project Status:** In Development  
**Project Type:** Minor Project (4th Semester) → Continuing in 5th & 6th Semesters  
**Student(s):** [@druvx13](https://github.com/druvx13) and team members  

📖 **See [PROJECT_PROPOSAL.md](PROJECT_PROPOSAL.md)** for complete project details  
📝 **See [STUDENT_MODIFICATIONS.md](STUDENT_MODIFICATIONS.md)** for detailed modification log  
⚖️ **See [ACADEMIC_USAGE.md](ACADEMIC_USAGE.md)** for GPL compliance and academic usage guidelines

---

## 📜 Project Overview

FluxBB is a fast, light, user-friendly forum application for your website. This specific repository is a **Visman fork** of FluxBB 1.5.11, which includes numerous enhancements, optimizations, and additional features over the standard FluxBB distribution.

The primary design goals of FluxBB are:
* **Speed:** Highly optimized code for fast page loads.
* **Simplicity:** A clean interface that stays out of the user's way.
* **Flexibility:** A robust plugin and addon system.

This fork ("FluxBB by Visman") introduces extended functionality such as improved BBCode parsing, subforum caching, enhanced security measures, and additional administration tools, making it a "battery-included" version of the classic forum software.

### 🎯 Student Enhancement Goals

As part of the academic minor project, the following enhancements are planned/implemented:

- 🔒 **Security Hardening:** Modern password hashing (Argon2/bcrypt), 2FA, rate limiting, SQL injection fixes
- ⚡ **Performance Optimization:** Redis caching, database query optimization, lazy loading
- 🎨 **Modern UI/UX:** Responsive design, dark mode, improved mobile experience
- 🔧 **New Features:** RESTful API, Markdown support, real-time notifications
- 🧪 **Quality Assurance:** Unit testing (PHPUnit), CI/CD pipeline, comprehensive documentation

**Progress:** See [STUDENT_MODIFICATIONS.md](STUDENT_MODIFICATIONS.md) for detailed implementation status.

---

## 🚀 Feature Inventory

### Core FluxBB Features
* **Clean Interface:** minimalist design focusing on content.
* **Standards Compliant:** Valid XHTML 1.0 Strict and CSS.
* **Database Support:** MySQL, PostgreSQL, and SQLite.
* **User Management:** Groups, permissions, banning, and profile customization.
* **Moderation:** Topic moving, splitting, merging, and sticky topics.

### Visman Fork Enhancements
* **Advanced BBCode:** Support for `[spoiler]`, `[video]`, `[audio]`, `[list=a]`, and more.
* **Performance:** Subforum caching and optimized database queries.
* **Security:** Integrated "Bad character" stripping (`forum_remove_bad_characters`) and anti-spam measures.
* **Search:** Enhanced search highlighting (`shl` parameter).
* **Usability:** "Quick Reply" enhancements and "Who is in this topic" features.
* **SEO:** Improved URL handling and meta data.

---

## 💻 System Requirements

To run this application, your environment must meet the following specifications:

* **Web Server:** Apache, Nginx, or IIS.
* **PHP:** Version 7.2 or higher.
  * *Extensions:* `mbstring`, `zlib` (recommended for gzip), `pdo` (for database drivers).
* **Database:** One of the following:
  * MySQL 5.5.3+ (InnoDB recommended)
  * PostgreSQL 7.0+
  * SQLite 3

---

## 🛠 Technology Stack

* **Language:** PHP (Procedural/Object-Oriented mix)
* **Frontend:** HTML5 / XHTML 1.0 Strict, CSS2/3
* **Scripting:** JavaScript (jQuery 1.12.4 included)
* **Database Abstraction:** Custom `DBLayer` (supports MySQLi, PgSQL, SQLite3)
* **Template Engine:** Custom lightweight template system (`.tpl` files)

---

## 📂 Directory Overview

| Directory | Purpose |
|-----------|---------|
| `/` | Root scripts (index, viewforum, viewtopic, admin, etc.) |
| `admin/` | (Virtual) Admin functionality is handled by `admin_*.php` files in root. |
| `addons/` | Security and functional extensions. |
| `cache/` | Writable directory for configuration and data caches. |
| `img/` | Static assets (avatars, smilies, icons). |
| `include/` | Core libraries, functions, and database layers. |
| `include/dblayer/` | Database abstraction drivers. |
| `include/template/` | HTML template files. |
| `js/` | JavaScript libraries and logic. |
| `lang/` | Localization files (English, French, Russian). |
| `plugins/` | Plugin system modules. |
| `style/` | CSS themes and layout definitions. |

---

## 📥 Installation Steps

1. **Clone the Repository:**
   ```bash
   git clone <repository-url> .
   ```

2. **Set Permissions:**
   Ensure the following directories are writable by the web server (e.g., `chmod 777` or `chown www-data`):
   * `cache/`
   * `img/avatars/`

3. **Web Install:**
   * Navigate to `http://your-domain.com/install.php`.
   * Follow the on-screen instructions to configure the database and administrator account.
   * This process generates `include/config.php`.

4. **Post-Install:**
   * **Delete `install.php`** for security.
   * Configure the forum via the Admin Control Panel.

---

## ⚙️ Configuration Reference

Configuration is split between:

1.  **File-based (`include/config.php`):**
    *   Database connection credentials (`$db_host`, `$db_name`, `$db_username`, `$db_password`).
    *   Table prefix (`$db_prefix`).
    *   Cookie setup (`$cookie_name`, `$cookie_domain`).

2.  **Database-based (Managed via Admin CP):**
    *   Site title and description.
    *   Timezone and date formats.
    *   Email settings (SMTP/Sendmail).
    *   Registration rules.
    *   **Note:** These settings are cached in `cache/cache_config.php`.

---

## 🗄 Database Setup

The installation script (`install.php`) handles the initial schema creation.

*   **Tables:** The system creates approximately 20 tables (prefixed, e.g., `pun_users`, `pun_topics`).
*   **Updates:** Database schema updates are handled by `db_update.php`. If the code version (`FORUM_VERSION`) exceeds the database version, the system redirects to this script.

---

## 🛡 Security Considerations

*   **Input Sanitization:** All user input is processed via `pun_htmlspecialchars` and `forum_remove_bad_characters`.
*   **SQL Injection:** Use of a database abstraction layer (`DBLayer`) prevents common SQLi attacks.
*   **CSRF:** Forms include tokens (`csrf_hash`) to prevent Cross-Site Request Forgery.
*   **Passwords:** Stored using secure hashing algorithms.

---

## ⚠️ Limitations & Assumptions

*   **Legacy Codebase:** The core architecture follows older PHP patterns (global variables, procedural logic).
*   **Cache Dependency:** The system relies heavily on the `cache/` directory. If this is not writable, the forum will fail.
*   **Theme Compatibility:** Themes designed for standard FluxBB 1.5 may require minor adjustments for this fork due to extra features.

---

## 📄 Licensing

This project is released under the **GNU General Public License (GPL) version 2 or higher**.
See the `LICENSE` file for the full legal text.

**Original Work Attribution:**
- Based on code by Rickard Andersson (PunBB)
- Based on FluxBB Team (FluxBB)
- Modified by Visman (FluxBB by Visman fork)
- **Enhanced by [@druvx13](https://github.com/druvx13)** and team for academic minor project (2025-2026)

### 🎓 Academic Usage & Attribution

This derivative work is being developed as part of a college minor project. All modifications are:
- ✅ Properly documented in [STUDENT_MODIFICATIONS.md](STUDENT_MODIFICATIONS.md)
- ✅ Released under the same GPL v2 license
- ✅ Fully attributed to original authors in [NOTICE.md](NOTICE.md)
- ✅ Compliant with academic integrity standards

**For students using this as reference:** Please review [ACADEMIC_USAGE.md](ACADEMIC_USAGE.md) to understand how to properly use open-source software for academic projects while maintaining ethical and legal compliance.

---

## 📚 Project Documentation

This repository includes comprehensive documentation for different audiences:

### For Students & Developers:
- 📋 **[PROJECT_PROPOSAL.md](PROJECT_PROPOSAL.md)** - Complete project definition, timeline, and deliverables
- 🔍 **[SUGGESTED_ENHANCEMENTS.md](SUGGESTED_ENHANCEMENTS.md)** - Detailed implementation guides for enhancements
- 📝 **[STUDENT_MODIFICATIONS.md](STUDENT_MODIFICATIONS.md)** - Log of all student modifications
- ⚖️ **[ACADEMIC_USAGE.md](ACADEMIC_USAGE.md)** - GPL compliance guide for academic use

### For Technical Reference:
- 🏗️ **[ARCHITECTURE.md](ARCHITECTURE.md)** - System design and request lifecycle
- 📂 **[DIRECTORY_STRUCTURE.md](DIRECTORY_STRUCTURE.md)** - Codebase organization
- 🔄 **[DATA_FLOW.md](DATA_FLOW.md)** - Data flow visualization
- 🔒 **[SECURITY.md](SECURITY.md)** - Security architecture and best practices

### For Administrators:
- ⚙️ **[CONFIGURATION.md](CONFIGURATION.md)** - Configuration reference
- 🚀 **[DEPLOYMENT.md](DEPLOYMENT.md)** - Deployment guide
- 👤 **[ADMIN_GUIDE.md](ADMIN_GUIDE.md)** - Administration manual
- 🔧 **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Common issues and solutions

### Legal & Compliance:
- 📜 **[LICENSE](LICENSE)** - GNU GPL v2 license text
- 📢 **[NOTICE.md](NOTICE.md)** - Third-party attributions
- 🔐 **[SECURITY_DISCLOSURE.md](SECURITY_DISCLOSURE.md)** - Vulnerability reporting

See **[DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)** for a complete overview.
