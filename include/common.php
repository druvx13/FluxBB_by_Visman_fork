<?php

/**
 * Copyright (C) 2008-2012 FluxBB
 * based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

if (!defined('PUN_ROOT'))
	exit('The constant PUN_ROOT must be defined and point to a valid FluxBB installation root directory.');

// Make sure PHP reports all errors except E_NOTICE. FluxBB supports E_ALL, but a lot of scripts it may interact with, do not
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

mb_language('uni');
mb_internal_encoding('UTF-8');
mb_substitute_character(0xFFFD);

// Record the start time (will be used to calculate the generation time for the page)
$pun_start = empty($_SERVER['REQUEST_TIME_FLOAT']) ? microtime(true) : (float) $_SERVER['REQUEST_TIME_FLOAT'];

// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.5.11');

define('FORUM_VER_REVISION', 87);	// номер сборки - Visman

$page_js = array();

define('FORUM_DB_REVISION', 21);
define('FORUM_SI_REVISION', 2.1);
define('FORUM_PARSER_REVISION', 2);

// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
{
	header('HTTP/1.1 403 Prefetching Forbidden');

	exit;
}

// Attempt to load the configuration file config.php
if (file_exists(PUN_ROOT.'include/config.php'))
	require PUN_ROOT.'include/config.php';

// If we have the 1.3-legacy constant defined, define the proper 1.4 constant so we don't get an incorrect "need to install" message
if (defined('FORUM'))
	define('PUN', FORUM);

// If PUN isn't defined, config.php is missing or corrupt
if (!defined('PUN'))
{
	header('Location: install.php');
	exit;
}

// Load the functions script
require PUN_ROOT.'include/functions.php';

// Load addon functionality
require PUN_ROOT.'include/addons.php';

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

// The addon manager is responsible for storing the hook listeners and communicating with the addons
$flux_addons = new flux_addon_manager();

// If a cookie name is not specified in config.php, we use the default (pun_cookie)
if (empty($cookie_name))
	$cookie_name = 'pun_cookie';

// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', PUN_ROOT.'cache/');

// Define a few commonly used constants
define('PUN_UNVERIFIED', 0);
define('PUN_ADMIN', 1);
define('PUN_MOD', 2);
define('PUN_GUEST', 3);
define('PUN_MEMBER', 4);

// Load DB abstraction layer and connect
require PUN_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$db->start_transaction();

// Load cached config
if (file_exists(FORUM_CACHE_DIR.'cache_config.php'))
	include FORUM_CACHE_DIR.'cache_config.php';

if (!defined('PUN_CONFIG_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_config_cache();
	require FORUM_CACHE_DIR.'cache_config.php';
}

// Verify that we are running the proper database schema revision
if (!isset($pun_config['o_database_revision']) || $pun_config['o_database_revision'] < FORUM_DB_REVISION ||
	!isset($pun_config['o_searchindex_revision']) || $pun_config['o_searchindex_revision'] < FORUM_SI_REVISION ||
	!isset($pun_config['o_parser_revision']) || $pun_config['o_parser_revision'] < FORUM_PARSER_REVISION ||
	!isset($pun_config['o_cur_ver_revision']) || $pun_config['o_cur_ver_revision'] < FORUM_VER_REVISION ||
	version_compare($pun_config['o_cur_version'], FORUM_VERSION, '<'))
{
	header('Location: db_update.php');
	exit;
}

// Enable output buffering
if (!defined('PUN_DISABLE_BUFFERING'))
{
	// Should we use gzip output compression?
	if ($pun_config['o_gzip'] && extension_loaded('zlib'))
		ob_start('ob_gzhandler');
	else
		ob_start();
}

// Define standard date/time formats
$forum_time_formats = array($pun_config['o_time_format'], 'H:i:s', 'H:i', 'g:i:s a', 'g:i a');
$forum_date_formats = array($pun_config['o_date_format'], 'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'M j Y', 'jS M Y');

// Check/update/set cookie and fetch user info
$pun_user = array();
check_cookie($pun_user);

// Attempt to load the common language file
if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/common.php'))
	include PUN_ROOT.'lang/'.$pun_user['language'].'/common.php';
else
	error('There is no valid language pack \''.pun_htmlspecialchars($pun_user['language']).'\' installed. Please reinstall a language of that name');

// Check if we are to display a maintenance message
if ($pun_config['o_maintenance'] && $pun_user['g_id'] > PUN_ADMIN && !defined('PUN_TURN_OFF_MAINT'))
	maintenance_message();

// Load cached bans
if (file_exists(FORUM_CACHE_DIR.'cache_bans.php'))
	include FORUM_CACHE_DIR.'cache_bans.php';

if (!defined('PUN_SVA_BANS_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_bans_cache();
	require FORUM_CACHE_DIR.'cache_bans.php';
}

// Check if current user is banned
check_bans();

// Update online list
$onl_u = $onl_g = $onl_s = array();
if (!defined('WITT_ENABLE')) // Кто в этой теме - Visman
	update_users_online();

// Check to see if we logged in without a cookie being set
if ($pun_user['is_guest'] && isset($_GET['login']))
	message($lang_common['No cookie']);

// The maximum size of a post, in bytes, since the field is now MEDIUMTEXT this allows ~16MB but lets cap at 1MB...
if (!defined('PUN_MAX_POSTSIZE'))
	define('PUN_MAX_POSTSIZE', 1048576);

if (!defined('PUN_SEARCH_MIN_WORD'))
	define('PUN_SEARCH_MIN_WORD', 3);
if (!defined('PUN_SEARCH_MAX_WORD'))
	define('PUN_SEARCH_MAX_WORD', 20);

if (!defined('FORUM_MAX_COOKIE_SIZE'))
	define('FORUM_MAX_COOKIE_SIZE', 4048);

// Load cached subforums - Visman
if (file_exists(FORUM_CACHE_DIR.'cache_subforums_'.$pun_user['g_id'].'.php'))
	include FORUM_CACHE_DIR.'cache_subforums_'.$pun_user['g_id'].'.php';

if (!isset($sf_array_tree))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_subforums_cache($pun_user['g_id']);
	require FORUM_CACHE_DIR.'cache_subforums_'.$pun_user['g_id'].'.php';
}
