# Changelog

All notable changes to this project will be documented in this file.

## [1.5.11-Visman] - Current Baseline

### Added
*   **Subforum Caching:** Reduced database load by caching forum hierarchy (`cache_subforums_*.php`).
*   **Advanced BBCode:** Added support for `[spoiler]`, `[video]`, `[audio]`, `[justify]`, `[right]`, `[center]`, `[hr]`, `[mono]`.
*   **Search Highlighting:** Added `shl` parameter and logic to highlight search terms in topics.
*   **Security Headers:** Enhanced `forum_remove_bad_characters` in `include/common.php`.
*   **Who Is In Topic:** Real-time(ish) tracking of users viewing a specific topic.
*   **Quick Reply:** Enhanced quick reply box with more BBCode buttons.
*   **Language Support:** Full Russian translation included in `lang/Russian/`.

### Changed
*   **Core:** Updated `FORUM_VERSION` to 1.5.11.
*   **Database:** `FORUM_DB_REVISION` set to 21.
*   **JS:** Bundled `jquery-1.12.4.min.js` and `media.min.js`.
*   **CSS:** Adjusted styles to support new BBCode elements (spoilers, quotes).

### Fixed
*   Various bug fixes backported from upstream and custom fixes by Visman.

---

## [1.5.10] - Upstream

### Fixed
*   See official FluxBB changelogs for historical context.
