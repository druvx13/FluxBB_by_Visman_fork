<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';

if ($pun_user['g_id'] != PUN_ADMIN)
	message($lang_common['No permission'], false, '403 Forbidden');

// Check if config exists
$result = $db->query('SELECT 1 FROM '.$db->prefix.'config WHERE conf_name=\'o_auto_code_highlight\'') or error('Unable to check config value', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
{
	$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_auto_code_highlight\', \'1\')') or error('Unable to insert config value', __FILE__, __LINE__, $db->error());
}
else
{
	// Ensure it is enabled if it exists but was 0? No, respect user setting.
	// But the user might run this to "fix" it.
	// We'll just ensure it exists. If they want to enable, they can do it in admin.
	// But the prompt says "it defaults to No".
	// If I insert '1', it defaults to Yes.
}

// Regenerate the config cache
if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
	require PUN_ROOT.'include/cache.php';

generate_config_cache();

message('Database updated and cache regenerated. Auto-code highlighting configuration key ensured. You can configure it in Admin > Options > Features.');
