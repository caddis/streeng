<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

include_once(PATH_THIRD . 'streeng/addon.setup.php');

$plugin_info = array (
	'pi_author' => STREENG_AUTHOR,
	'pi_author_url' => STREENG_AUTHOR_URL,
	'pi_description' => STREENG_DESC,
	'pi_name' => STREENG_NAME,
	'pi_version' => STREENG_VER,
	'pi_usage' => Streeng::usage()
);

class Streeng
{
	public $return_data = '';

	public function __construct() {
		$string = ee()->TMPL->tagdata;

		// Strip tags
		$allowed = ee()->TMPL->fetch_param('allowed');

		if ($allowed !== false) {
			$tags = explode('|', $allowed);
			$allow = '<' . implode('>,<', $tags) . '>';

			$string = ($allowed === 'none') ?
				strip_tags($string) :
				strip_tags($string, $allow);
		}

		// Find and replace
		$find = ee()->TMPL->fetch_param('find');

		if ($find !== false) {
			$replace = ee()->TMPL->fetch_param('replace');
			$insensitive = ee()->TMPL->fetch_param('insensitive');

			if ($replace !== false) {
				// Options
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

				$explode = ee()->TMPL->fetch_param('explode', '|');

				$find = explode($explode, $find);
				$replace = explode($explode, $replace);

				foreach ($find as $i => $search) {
					$search = isset($searchOptions[$search]) ?
						$searchOptions[$search] :
						$search;
					$search = $this->_prep_regex($search, $insensitive, $flags);

					$replacement = isset($replace[$i]) ?
						$replace[$i] :
						$replace[0];
					$replacement = isset($replacementOptions[$replacement]) ?
						$replacementOptions[$replacement] :
						$replacement;

					$string = preg_replace($search, $replacement, $string);
				}
			} else {
				$this->return_data = ((
					$insensitive ?
						stripos($string, $find) :
						strpos($string, $find)
					) !== false) ? 1 : 0;

				return;
			}
		}

		// Trim whitespace
		$trim = ee()->TMPL->fetch_param('trim', 'both');

		if ($trim !== 'no') {
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

		if ($url === 'yes') {
			$string = urlencode($string);
		} elseif ($url === 'raw') {
			$string = rawurlencode($string);
		}

		// HTML encode
		$encode = ee()->TMPL->fetch_param('encode');

		if ($encode === 'yes') {
			$encode_flags = constant(
				ee()->TMPL->fetch_param('encode_flags', 'ENT_COMPAT')
			);

			$encode_encoding = ee()->TMPL->fetch_param(
				'encode_encoding',
				'UTF-8'
			);

			$string = htmlentities($string, $encode_flags, $encode_encoding);
		}

		// HTML decode
		$decode = ee()->TMPL->fetch_param('decode');

		if ($decode === 'yes') {
			$decode_flags = constant(
				ee()->TMPL->fetch_param('decode_flags', 'ENT_COMPAT')
			);

			$decode_encoding = ee()->TMPL->fetch_param('decode_flags', 'UTF-8');

			$string = html_entity_decode(
				$string,
				$decode_flags,
				$decode_encoding
			);
		}

		// HTML special characters
		$specialchars = ee()->TMPL->fetch_param('specialchars');

		if ($specialchars === 'yes') {
			$specialchars_flags = constant(
				ee()->TMPL->fetch_param('specialchars_flags', 'ENT_COMPAT')
			);

			$specialchars_encoding = ee()->TMPL->fetch_param(
				'decode_flags',
				'UTF-8'
			);

			$string = htmlspecialchars(
				$string,
				$specialchars_flags,
				$specialchars_encoding
			);
		}

		// Capitalize
		$capitalize = ee()->TMPL->fetch_param('capitalize');

		if ($capitalize === 'yes') {
			$string = ucfirst($string);
		}

		// Title case
		$title = ee()->TMPL->fetch_param('title');

		if ($title === 'yes') {
			$string = ucwords(strtolower($string));
		}

		// Lowercase
		$lower = ee()->TMPL->fetch_param('lower');

		if ($lower === 'yes') {
			$string = strtolower($string);
		}

		// Uppercase
		$upper = ee()->TMPL->fetch_param('upper');

		if ($upper === 'yes') {
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

			if ($allowed === 'none') {
				$string = strlen($temp_string) < strlen($string) ?
					($temp_string . $append) :
					$temp_string;
			} else {
				$characters = strlen($temp_string);
				$string = $this->_truncate_markup($string, $characters, $append, true, true);
			}
		} elseif ($characters !== 0) {
			if ($allowed === 'none') {
				$temp_string = strip_tags($string);

				if (strlen($temp_string) > $characters) {
					$pos = strrpos(substr($temp_string, 0, $characters), ' ');
					$string = substr($temp_string, 0, $pos) . $append;
				}
			} else {
				$string = $this->_truncate_markup(
					$string, $characters, $append, true, true
				);
			}
		}

		// Slug
		$slug = ee()->TMPL->fetch_param('slug');

		if ($slug === 'yes') {
			$separator = ee()->TMPL->fetch_param('separator', '-');

			$string = preg_replace('/[^A-Za-z0-9-]+/', $separator, $string);
		}

		// Parse typography
		$typography = ee()->TMPL->fetch_param('typography');

		if ($typography !== false) {
			$typographyAllowed = array(
				'all',
				'xhtml',
				'br',
				'lite'
			);

			if (in_array($typography, $typographyAllowed)) {
				ee()->load->library('typography');

				ee()->typography->initialize();

				$typographyPrefs = array(
					'text_format' => $typography,
					'html_format' => 'all',
					'auto_links' => 'n',
					'allow_img_url' => 'y'
				);

				$string = ee()->typography->parse_type(
					$string,
					$typographyPrefs
				);
			}
		}

		// Repeat
		$repeat = (int) ee()->TMPL->fetch_param('repeat', 0);

		if ($repeat > 0) {
			$string .= str_repeat($string, $repeat);
		}

		// Substring count
		$count = ee()->TMPL->fetch_param('count');

		if ($count !== false) {
			if ($count === 'ALL') {
				$string = strlen($string);
			} else {
				$string = substr_count($string, $count);
			}
		}

		// Split count
		$splits = ee()->TMPL->fetch_param('splits');

		if ($splits !== false) {
			$string = count(explode($splits, $string));
		}

		$this->return_data = $string;
	}

	private function _prep_regex($string, $insensitive = true, $flags = false)
	{
		// Check containing characters
		if (substr($string, 0, 1) !== '/' || substr($string, 0, 2) === '\/') {
			$string = '/' . $string;
		}

		if (substr($string, -1, 1) !== '/' || substr($string, -2, 2) === '\/') {
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

	private function _truncate_markup($markup, $length = 400, $appendix = '…', $appendixInside = false, $wordsafe = false) {
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
				} elseif ($tag[strlen($tag) - 2] === '/') {
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
				// Search the last occurrence of a space
				$spacepos = strrpos($truncated, ' ');

				if ($spacepos !== false) {
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

	public static function usage() {
		return 'See docs and examples at https://github.com/caddis/streeng';
	}
}
