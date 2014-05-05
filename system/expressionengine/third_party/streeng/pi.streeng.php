<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Streeng',
	'pi_version' => '1.5.1',
	'pi_author' => 'Caddis',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Perform common operations on strings.',
	'pi_usage' => Streeng::usage()
);

class Streeng {

	public $return_data = '';

	public function __construct()
	{
		// Set string to tagdata

		$string = ee()->TMPL->tagdata;

		// Strip tags

		$allowed = ee()->TMPL->fetch_param('allowed');

		if ($allowed !== false) {
			$tags = explode('|', $allowed);
			$allow = '<' . implode('>,<', $tags) . '>';

			$string = ($allowed == 'none') ? strip_tags($string) : strip_tags($string, $allow);
		}

		// Find & Replace

		$find = ee()->TMPL->fetch_param('find');

		if ($find !== false) {
			$replace = ee()->TMPL->fetch_param('replace');
			$insensitive = ee()->TMPL->fetch_param('insensitive');

			$explode = ee()->TMPL->fetch_param('explode', '|');

			$find = explode($explode, $find);

			if ($replace !== false) {
				// Options

				$regex = ee()->TMPL->fetch_param('regex');
				$flags = ee()->TMPL->fetch_param('flags');

				// Search options

				$searchOptions = array(
					'NEWLINE' => "\n",
					'PIPE'    => '\|',
					'QUOTE'   => '"',
					'SPACE'   => ' '
				);

				// Replacement options

				$replacementOptions = array(
					'NEWLINE' => "\n",
					'PIPE'    => '|',
					'QUOTE'   => '"',
					'SPACE'   => ' '
				);

				$replace = explode($explode, $replace);

				foreach ($find as $i => $search) {
					$search = isset($searchOptions[$search]) ? $searchOptions[$search] : $search;
					$search = $this->_prep_regex($search, $insensitive, $flags);

					$replacement = isset($replace[$i]) ? $replace[$i] : $replace[0];
					$replacement = isset($replacementOptions[$replacement]) ? $replacementOptions[$replacement] : $replacement;

					$string = preg_replace($search, $replacement, $string);
				}
			} else {
				$this->return_data = ((($insensitive) ? stripos($string, $find) : strpos($string, $find)) !== false) ? 1 : 0;

				return;
			}
		}

		// Trim white space

		$trim = ee()->TMPL->fetch_param('trim', 'both');

		if ($trim != 'no') {
			switch ($trim) {
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

		if ($url == 'yes') {
			$string = urlencode($string);
		}

		// HTML encode

		$encode = ee()->TMPL->fetch_param('encode');

		if ($encode == 'yes') {
			$string = htmlentities($string);
		}

		// HTML decode

		$decode = ee()->TMPL->fetch_param('decode');

		if ($decode == 'yes') {
			$string = html_entity_decode($string);
		}

		// Capitalize

		$capitalize = ee()->TMPL->fetch_param('capitalize');

		if ($capitalize == 'yes') {
			$string = ucfirst($string);
		}

		// Title case

		$title = ee()->TMPL->fetch_param('title');

		if ($title == 'yes') {
			$string = ucwords(strtolower($string));
		}

		// Lowercase

		$lower = ee()->TMPL->fetch_param('lower');

		if ($lower == 'yes') {
			$string = strtolower($string);
		}

		// Uppercase

		$upper = ee()->TMPL->fetch_param('upper');

		if ($upper == 'yes') {
			$string = strtoupper($string);
		}

		// Truncate

		$characters = (int) ee()->TMPL->fetch_param('characters');
		$words = (int) ee()->TMPL->fetch_param('words');
		$append = ee()->TMPL->fetch_param('append', '&hellip;');

		if ($words !== 0) {
			$temp_string = strip_tags($string);
			$temp_string = explode(' ', $temp_string);
			$temp_string = implode(' ', array_splice($temp_string, 0, $words + 1));

			if ($allowed == 'none') {
				$string = (strlen($temp_string) < strlen($string)) ? ($temp_string . $append) : $temp_string;
			} else {
				$characters = strlen($temp_string);
				$string = $this->_truncate_markup($string, $characters, $append, true, true);
			}
		} else if ($characters !== 0) {
			if ($allowed == 'none') {
				$temp_string = strip_tags($string);

				if (strlen($temp_string) > $characters) {
					$string = substr($temp_string, 0, strrpos(substr($temp_string, 0, $characters), ' ')) . $append;
				}
			} else {
				$string = $this->_truncate_markup($string, $characters, $append, true, true);
			}
		}

		// Slug

		$slug = ee()->TMPL->fetch_param('slug');

		if ($slug == 'yes') {
			$separator = ee()->TMPL->fetch_param('separator', '-');

			$string = preg_replace('/[^A-Za-z0-9-]+/', $separator, $string);
		}

		// Repeat

		$repeat = (int) ee()->TMPL->fetch_param('repeat', 0);

		if ($repeat > 0) {
			$string .= str_repeat($string, $repeat);
		}

		// Substring count

		$count = ee()->TMPL->fetch_param('count');

		if ($count !== false) {
			$string = substr_count($string, $count);
		}

		// Split count

		$splits = ee()->TMPL->fetch_param('splits');

		if ($splits !== false) {
			$string = count(explode($splits, $string));
		}

		$this->return_data = $string;
	}

	function _prep_regex($string, $insensitive = true, $flags = false)
	{
		// Check containing characters

		if (substr($string, 0, 1) != '/' or substr($string, 0, 2) == '\/') {
			$string = '/' . $string;
		}

		if (substr($string, -1, 1) != '/' or substr($string, -2, 2) == '\/') {
			$string .= '/';
		}

		// Pattern modifiers

		if ($flags) {
			$string .= str_replace('i', '', $flags);
		}

		if (! $insensitive) {
			$string .= 'i';
		}

		return $string;
	}

	/**
	 * @package   php-shorten
	 * @example   example.html.php
	 * @link      https://github.com/Dreamseer/php-shorten/
	 * @author    Marc Görtz (http://marcgoertz.de/)
	 * @license   MIT License
	 * @copyright Copyright (c) 2011-2013, Marc Görtz
	 * @version   1.1.0
	 */

	private function _truncate_markup($markup, $length = 400, $appendix = '…', $appendixInside = FALSE, $wordsafe = FALSE)
	{
		$truncated = '';
		$lengthOutput = 0;
		$position = 0;
		$tags = array();

		// To avoid UTF-8 multibyte glitches we need entities, but no special characters for tags or existing entities
		$markup = str_replace(array(
			'&lt;', '&gt;', '&amp;',
		), array(
			'<', '>', '&',
		), htmlentities($markup, ENT_NOQUOTES, 'UTF-8'));

		// Loop through text
		while ($lengthOutput < $length && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $markup, $match, PREG_OFFSET_CAPTURE, $position)) {
			list($tag, $positionTag) = $match[0];

			// Add text leading up to the tag or entity
			$text = substr($markup, $position, $positionTag - $position);

			if ($lengthOutput + strlen($text) > $length) {
				$truncated .= substr($text, 0, $length - $lengthOutput);
				$lengthOutput = $length;

				break;
			}

			$truncated .= $text;
			$lengthOutput += strlen($text);

			// Add tags and entities
			if ($tag[0] === '&') {
				// Handle the entity
				$truncated .= $tag;
				// Which is only one character
				$lengthOutput++;
			} else {
				// Handle the tag
				$tagName = $match[1][0];

				if ($tag[1] === '/') {
					// This is a closing tag
					$openingTag = array_pop($tags);
					// Check that tags are properly nested
					assert($openingTag === $tagName);
					$truncated .= $tag;
				} else if ($tag[strlen($tag) - 2] === '/') {
					// Self-closing tag in XML dialect
					$truncated .= $tag;
				} else {
					// Opening tag
					$truncated .= $tag;
					$tags[] = $tagName;
				}
			}

			// Continue after the tag
			$position = $positionTag + strlen($tag);
		}

		// Add any remaining text
		if ($lengthOutput < $length && $position < strlen($markup)) {
			$truncated .= substr($markup, $position, $length - $lengthOutput);
		}

		if (strlen($truncated) < strlen($markup)) {
			// If the words shouldn't be cut in the middle
			if ($wordsafe) {
				// Search the last occurance of a space
				$spacepos = strrpos($truncated, ' ');

				if (isset($spacepos)) {
					// Cut the text in this position
					$truncated = substr($truncated, 0, $spacepos);
				}
			}

			// Add appendix to last tag content
			if ($appendixInside) {
				$truncated .= $appendix;
			}

			// Close any open tags
			while (! empty($tags)) {
				$truncated .= sprintf('</%s>', array_pop($tags));
			}

			return ($appendixInside) ? $truncated : $truncated . $appendix;
		}

		return $truncated;
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