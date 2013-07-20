<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Streeng',
	'pi_version' => '1.0.2',
	'pi_author' => 'Michael Leigeber',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Perform common operations on strings.',
	'pi_usage' => Streeng::usage()
);

class Streeng {

	public $return_data = '';

	// Truthiness Check
	private function _is_truthy($s)
	{
		switch(strtolower((string) $s))
		{
			case '': case '0': case 'n': case 'no': case 'off': case 'false': return FALSE;
			default: return TRUE;
		}
	}

	public function __construct()
	{
		$this->EE =& get_instance();

		$this->EE->load->helper('string');
		$this->EE->load->helper('text');

		// Set string to tagdata

		$string = $this->EE->TMPL->tagdata;

		// Set case sensitivity

		$insensitive = $this->_is_truthy($this->EE->TMPL->fetch_param('insensitive'));

		// Strip tags

		$allowed = $this->EE->TMPL->fetch_param('allowed');

		if ($allowed)
		{
			$string = ($allowed == 'none') ? strip_tags($string) : strip_tags($string, $allowed);
		}

		// Find & Replace

		$find = $this->EE->TMPL->fetch_param('find');
		$replace = $this->EE->TMPL->fetch_param('replace');

		if ($find && $replace !== FALSE)
		{
			$fn = ($insensitive)? 'str_ireplace': 'str_replace';
			$string = call_user_func_array($fn, array($find, $replace, $string));
		}

		// Find (No Replace)

		if ($find && $replace === FALSE)
		{
			$fn = ($insensitive)? 'stripos': 'strpos';
			$string = (call_user_func_array($fn, array($string, $find)) !== FALSE)? 1: 0;
		}

		// Trim white space

		$trim = $this->EE->TMPL->fetch_param('trim', 'both');

		if ($trim != 'no')
		{
			switch ($trim)
			{
				case 'both':
					$string = trim($string);
				case 'left':
					$string = ltrim($string);
				default:
					$string = rtrim($string);
			}
		}

		// HTML encode

		$encode = $this->EE->TMPL->fetch_param('encode');

		if ($encode == 'yes')
		{
			$string = htmlentities($string);
		}

		// HTML decode

		$decode = $this->EE->TMPL->fetch_param('decode');

		if ($decode == 'yes')
		{
			$string = html_entity_decode($string);
		}

		// Capitalize

		$capitalize = $this->EE->TMPL->fetch_param('capitalize');

		if ($capitalize == 'yes')
		{
			$string = ucfirst($string);
		}

		// Title case

		$title = $this->EE->TMPL->fetch_param('title');

		if ($title == 'yes')
		{
			$string = ucwords(strtolower($string));
		}

		// Lower case

		$lower = $this->EE->TMPL->fetch_param('lower');

		if ($lower == 'yes')
		{
			$string = strtolower($string);
		}

		// Upper case

		$upper = $this->EE->TMPL->fetch_param('upper');

		if ($upper == 'yes')
		{
			$string = strtoupper($string);
		}

		// Truncate

		$characters = (int) $this->EE->TMPL->fetch_param('characters');
		$words = (int) $this->EE->TMPL->fetch_param('words');
		$append = $this->EE->TMPL->fetch_param('append', '&hellip;');

		if ($words)
		{
			$string = word_limiter($string, $words, $append);
		}
		else if ($characters)
		{
			$string = character_limiter($string, $characters, $append);
		}

		// Slug

		$slug = $this->EE->TMPL->fetch_param('slug');

		if ($slug == 'yes')
		{
			$separator = $this->EE->TMPL->fetch_param('separator', '-');

			$string = preg_replace('/[^A-Za-z0-9-]+/', $separator, $string);
		}

		// // Repeat

		$repeat = (int) $this->EE->TMPL->fetch_param('repeat', 0);

		if ($repeat > 0)
		{
			$string .= repeater($string, $repeat);
		}

		$this->return_data = $string;
	}

	function usage()
	{
		ob_start();
?>
Parameters:

allowed="p|span|a" - pass "none" to strip all tags or a | delimited list of allowed tags (defaults = allow al)
find="string1" - string to find (default = false)
replace="string2" - string to replace found string (default = "")
insensitive="yes" - perform case insensitive find or replace (default="no")
trim="left" - left, right, or both (default = "both")
encode="yes" - HTML encode the string (default = "no")
decode="yes" - HTML decode the string (default = "no")
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

Usage:

{exp:streeng allowed="<p>" title="yes" repeat="2" find=" a " replace=" my "}  <p><b>This</b> is a <a href="#">test</a>.</p>{/exp:streeng}

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