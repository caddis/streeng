<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Streeng',
	'pi_version' => '1.0.1',
	'pi_author' => 'Michael Leigeber',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Perform common operations on strings.',
	'pi_usage' => Streeng::usage()
);

class Streeng {

	public $return_data = '';

	public function __construct()
	{
		$this->EE =& get_instance();

		ee()->load->helper('string');
		ee()->load->helper('text');

		// Set string to tagdata

		$string = $this->EE->TMPL->tagdata;

		// Strip tags

		$allowed = $this->EE->TMPL->fetch_param('allowed');

		if ($allowed)
		{
			$string = ($allowed == 'none') ? strip_tags($string) : strip_tags($string, $allowed);
		}

		// Replace

		$find = $this->EE->TMPL->fetch_param('find');

		if ($find)
		{
			$replace = $this->EE->TMPL->fetch_param('replace', '');

			$string = str_replace($find, $replace, $string);
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
Usage:

Coming soon
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}
?>