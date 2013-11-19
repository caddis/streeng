<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Streeng',
	'pi_version' => '1.4.0',
	'pi_author' => 'Caddis',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Perform common operations on strings.',
	'pi_usage' => Streeng::usage()
);

class Streeng {

	public $return_data = '';

	public function __construct()
	{
		ee()->load->helper('string');
		ee()->load->helper('text');

		// Set string to tagdata

		$string = ee()->TMPL->tagdata;

		// Strip tags

		$allowed = ee()->TMPL->fetch_param('allowed');

		if ($allowed !== false)
		{
			$tags = explode('|', $allowed);
			$allow = '<' . implode('>,<', $tags) . '>';

			$string = ($allowed == 'none') ? strip_tags($string) : strip_tags($string, $allow);
		}

		// Find & Replace

		$find = ee()->TMPL->fetch_param('find');

		if ($find !== false)
		{
			$replace = ee()->TMPL->fetch_param('replace');
			$insensitive = ee()->TMPL->fetch_param('insensitive');

			$find = explode('|', $find);

			if ($replace !== false)
			{
				// Options

				$regex = ee()->TMPL->fetch_param('regex');
				$flags = ee()->TMPL->fetch_param('flags');

				$replace = explode('|', $replace);

				// Replacements

				$replacementOptions = array(
					'NEWLINE' => "\n",
					'PIPE'    => '|',
					'QUOTE'   => '"',
					'SPACE'   => ' '
				);

				foreach ($find as $i => $search)
				{
					$search = $this->_prep_regex($search, $insensitive, $flags);

					$replacement = isset($replace[$i]) ? $replace[$i] : $replace[0];
					$replacement = isset($replacementOptions[$replacement]) ? $replacementOptions[$replacement] : $replacement;

					$string = preg_replace($search, $replacement, $string);
				}
			}
			else
			{
				$this->return_data = ((($insensitive) ? stripos($string, $find) : strpos($string, $find)) !== false) ? 1 : 0;

				return;
			}
		}

		// Trim white space

		$trim = ee()->TMPL->fetch_param('trim', 'both');

		if ($trim != 'no')
		{
			switch ($trim)
			{
				case 'both':
					$string = trim($string);
					break;
				case 'left':
					$string = ltrim($string);
					break;
				default:
					$string = rtrim($string);
			}
		}

		// URL encode

		$url = ee()->TMPL->fetch_param('url');

		if ($url == 'yes')
		{
			$string = urlencode($string);
		}

		// HTML encode

		$encode = ee()->TMPL->fetch_param('encode');

		if ($encode == 'yes')
		{
			$string = htmlentities($string);
		}

		// HTML decode

		$decode = ee()->TMPL->fetch_param('decode');

		if ($decode == 'yes')
		{
			$string = html_entity_decode($string);
		}

		// Capitalize

		$capitalize = ee()->TMPL->fetch_param('capitalize');

		if ($capitalize == 'yes')
		{
			$string = ucfirst($string);
		}

		// Title case

		$title = ee()->TMPL->fetch_param('title');

		if ($title == 'yes')
		{
			$string = ucwords(strtolower($string));
		}

		// Lowercase

		$lower = ee()->TMPL->fetch_param('lower');

		if ($lower == 'yes')
		{
			$string = strtolower($string);
		}

		// Uppercase

		$upper = ee()->TMPL->fetch_param('upper');

		if ($upper == 'yes')
		{
			$string = strtoupper($string);
		}

		// Truncate

		$characters = (int) ee()->TMPL->fetch_param('characters');
		$words = (int) ee()->TMPL->fetch_param('words');
		$append = ee()->TMPL->fetch_param('append', '&hellip;');

		if ($words !== 0)
		{
			$string = word_limiter($string, $words, $append);
		}
		else if ($characters !== 0)
		{
			$string = character_limiter($string, $characters, $append);
		}

		// Auto-close tags when truncates

		$autoclose = ee()->TMPL->fetch_param('autoclose');

		if ($autoclose !== false and ($words !== 0 or $characters !== 0))
		{
			$string = $this->_close_tags($string, $autoclose);
		}

		// Slug

		$slug = ee()->TMPL->fetch_param('slug');

		if ($slug == 'yes')
		{
			$separator = ee()->TMPL->fetch_param('separator', '-');

			$string = preg_replace('/[^A-Za-z0-9-]+/', $separator, $string);
		}

		// Repeat

		$repeat = (int) ee()->TMPL->fetch_param('repeat', 0);

		if ($repeat > 0)
		{
			$string .= repeater($string, $repeat);
		}

		// Substring count

		$count = ee()->TMPL->fetch_param('count');

		if ($count !== false)
		{
			$string = substr_count($string, $count);
		}

		// Split count

		$splits = ee()->TMPL->fetch_param('splits');

		if ($splits !== false)
		{
			$string = count(explode($splits, $string));
		}

		$this->return_data = $string;
	}

	function _prep_regex($string, $insensitive = true, $flags = false)
	{
		// Check containing characters

		if (substr($string, 0, 1) != '/')
		{
			$string = '/' . $string;
		}

		if (substr($string, -1, 1) != '/')
		{
			$string .= '/';
		}

		// Pattern modifiers

		if ($flags)
		{
			$string .= str_replace('i', '', $flags);
		}

		if (! $insensitive)
		{
			$string .= 'i';
		}

		return $string;
	}

	private function _close_tags($string, $mode)
	{
		// Use Tidy if available else use DOMDocument

		if (extension_loaded('tidy'))
		{
			$html = new tidy();

			$html->parseString($string, array(
				'show-body-only' => true,
				'output-xhtml' => ($mode == 'html') ? false : true
			), 'utf8');

			$html->cleanRepair();

			return $html;
		}

		$doc = new DOMDocument();
		$doc->loadHTML("$string");

		$html = ($mode == 'html') ? $doc->saveHTML($doc) : $doc->saveXML($doc);
		$html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);

		return $html;
	}

	function usage()
	{
		ob_start();
?>
Parameters:

allowed="<p>|<span>|<a>" - pass "none" to strip all tags or a | delimited list of allowed tags (defaults = allow al)
find="string1" - string to find (default = false)
replace="string2" - string to replace found string (default = "")
trim="left" - left, right, or both (default = "both")
encode="yes" - HTML encode the string (default = "no")
decode="yes" - HTML decode the string (default = "no")
url="yes" - URL encode the string (default = "no")
capitalize="yes" - capitalize the first character of the string (default = "no")
title="yes" - capitalize the first character of every word (default = "no")
lower="yes" - convert the string to lower case (default = "no")
upper="yes" - convert the string to upper case (default = "no")
characters="10" - number of characters to truncate the string to (default = unlimited)
words="10" - number of words to truncate the string to (default = unlimited)
append="..." - if trucated append this to the end of the string (default = "&hellip;")
slug="yes" - convert the string to a slug (default = "no")
separator="_" - seperator for slug (default = "-")
repeat="3" - number of times to repeat the string, great for prototyping (default = 0)
insensitive="yes" - toggle case sensitivity when finding a string (default = "no")
count="|" - return number of substring instances of a supplied string (default = false)
splits="|" - return number of exploded values from a supplied string (default = false)
autoclose="html" - toggle markup mode (html|xhtml) for auto-closing open tags (default = disable autoclose)

Usage:

{exp:streeng allowed="<p>" title="yes" repeat="2" find=" a " replace=" my "}<p><b>This</b> is a <a href="#">test</a>.</p>{/exp:streeng}

{if "{exp:streeng find='this' insensitive='yes'}This is a test string{/exp:streeng}"}
We found 'this' in 'This is a test string'!
{/if}

<p>This Is My Test</p>
<p>This Is My Test</p>
<p>This Is My Test</p>
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}
?>