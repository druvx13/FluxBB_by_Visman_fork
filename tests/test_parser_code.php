<?php
// Mock FluxBB environment
define('PUN', 1);
define('FORUM_CACHE_DIR', 'cache/');
define('PUN_ROOT', './');

// Mock config
$pun_config = array(
    'o_censoring' => '0',
    'p_message_bbcode' => '1',
    'o_smilies' => '0',
    'o_base_url' => 'http://example.com',
    'o_auto_code_highlight' => '1', // Enable the feature
    'o_quote_depth' => 3,
    'o_make_links' => 0,
    'o_indent_num_spaces' => 4,
    'p_sig_img_tag' => 0,
    'p_message_img_tag' => 1
);
$pun_user = array(
    'show_smilies' => '0',
    'g_post_links' => '1',
    'show_img' => '1',
    'show_img_sig' => '0'
);
$lang_common = array(
    'BBCode error no closing tag' => 'No closing tag',
    'BBCode error invalid nesting' => 'Invalid nesting',
    'BBCode error empty attribute' => 'Empty attribute',
    'Image link' => 'Image'
);
$lang_post = array();
$smilies = array(); // Mock smilies
$re_list = '%\[list(?:=([1a*]))?+\]((?:[^\[]*+(?:(?!\[list(?:=[1a*])?+\]|\[/list\])\[[^\[]*+)*+|(?R))*)\[/list\]%i';


// Mock functions
function pun_htmlspecialchars($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
function pun_trim($str, $charlist = null) {
    if ($charlist) return trim($str, $charlist);
    return trim($str);
}
function censor_words($text) { return $text; }
// Note: do_bbcode and clean_paragraphs are in parser.php, do not mock them.

function extract_blocks($text, $start, $end, $retab = true) {
    // We mock extract_blocks because it is in functions.php, but parser.php calls it.
    // Basic mock for legacy fallback or if parser.php doesn't define it (it doesn't, it uses it).
    // The parser.php implementation calls extract_blocks in standard code extraction.

    // Simple implementation for [code]...[/code]
    $code = array();
	$start_len = strlen($start);
	$end_len = strlen($end);
	$regex = '%(?:'.preg_quote($start, '%').'|'.preg_quote($end, '%').')%';
	$matches = array();

	if (preg_match_all($regex, $text, $matches))
	{
		$counter = $offset = 0;
		$start_pos = $end_pos = false;

		foreach ($matches[0] as $match)
		{
			if ($match == $start)
			{
				if ($counter == 0)
					$start_pos = strpos($text, $start);
				$counter++;
			}
			elseif ($match == $end)
			{
				$counter--;
				if ($counter == 0)
					$end_pos = strpos($text, $end, $offset + 1);
				$offset = strpos($text, $end, $offset + 1);
			}

			if ($start_pos !== false && $end_pos !== false)
			{
				$code[] = substr($text, $start_pos + $start_len,
					$end_pos - $start_pos - $start_len);
				$text = substr_replace($text, "\1", $start_pos,
					$end_pos - $start_pos + $end_len);
				$start_pos = $end_pos = false;
				$offset = 0;
			}
		}
	}
    return array($code, $text);
}

function get_base_url($support_https = false) {
    return 'http://example.com';
}
function get_current_protocol() { return 'http'; }

// Include the parser
include 'include/parser.php';

// Test Cases
$tests = [
    "Triple backticks" => [
        "input" => "Here is code:\n```php\necho 'hello';\n```",
        "expected_contain" => '<code class="language-php">echo &#039;hello&#039;;</code>'
    ],
    "Triple backticks no lang" => [
        "input" => "Code:\n```\nvar a = 1;\n```",
        "expected_contain" => '<code>var a = 1;</code>'
    ],
    "BBCode with lang" => [
        "input" => "[code=python]print('hi')[/code]",
        "expected_contain" => '<code class="language-python">print(&#039;hi&#039;)</code>'
    ],
    "BBCode standard" => [
        "input" => "[code]alert(1);[/code]",
        "expected_contain" => '<code>alert(1);</code>'
    ],
    "Indented block" => [
        "input" => "Text\n\n    var x = 1;\n    var y = 2;",
        "expected_contain" => '<code>var x = 1;'."\n".'var y = 2;</code>'
    ],
    "HTML pre code" => [
        "input" => "<pre><code><div></div></code></pre>",
        "expected_contain" => '<code class="language-html">&lt;div&gt;&lt;/div&gt;</code>'
    ],
    "XSS Check" => [
        "input" => "```\n<script>alert(1)</script>\n```",
        "expected_contain" => '&lt;script&gt;alert(1)&lt;/script&gt;'
    ]
];

$failed = 0;
foreach ($tests as $name => $data) {
    // Reset global errors
    $errors = [];

    $output = parse_message($data['input'], 0);
    // Normalize newlines for comparison
    $output = str_replace("\r", "", $output);
    $data['expected_contain'] = str_replace("\r", "", $data['expected_contain']);

    if (strpos($output, $data['expected_contain']) !== false) {
        echo "PASS: $name\n";
    } else {
        echo "FAIL: $name\n";
        echo "Expected to contain: " . htmlspecialchars($data['expected_contain']) . "\n";
        echo "Got: " . htmlspecialchars($output) . "\n";
        $failed++;
    }
}

if ($failed > 0) exit(1);
echo "All tests passed.\n";
