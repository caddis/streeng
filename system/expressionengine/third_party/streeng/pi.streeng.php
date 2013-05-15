<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Streeng',
	'pi_version' => '1.0.0',
	'pi_author' => 'Michael Leigeber',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Perform common operations on strings.',
	'pi_usage' => Streeng::usage()
);

class Streeng {

	public function __construct()
	{
		$this->EE =& get_instance();
	}

	public function capitalize()
	{
		return ucfirst($this->EE->TMPL->tagdata);
	}

	public function camel()
	{
		return ucwords(strtolower($this->EE->TMPL->tagdata));
	}

	public function upper()
	{
		return strtoupper($this->EE->TMPL->tagdata);
	}

	public function lower()
	{
		return strtolower($this->EE->TMPL->tagdata);
	}

	public function strip()
	{
		$allowed = $this->EE->TMPL->fetch_param('allowed');

		return ($allowed_tags) ? strip_tags($this->EE->TMPL->tagdata, $allowed) : strip_tags($this->EE->TMPL->tagdata);
	}

	public function truncate()
	{
		$tagdata = $this->EE->TMPL->tagdata;

		$limit = (int) $this->EE->TMPL->fetch_param('limit');
		$append = $this->EE->TMPL->fetch_param('append', '');

		return (strlen($tagdata) > $limit) ? substr($tagdata, 0, $limit) . $append : $tagdata;
	}

	public function replace()
	{
		$find = $this->EE->TMPL->fetch_param('find');
		$replace = $this->EE->TMPL->fetch_param('replace');

		return str_replace($find, $replace, $this->EE->TMPL->tagdata);
	}

	public function slug()
	{
		$separator = $this->EE->TMPL->fetch_param('separator', '-');

		return preg_replace('/[^A-Za-z0-9-]+/', $separator, $this->EE->TMPL->tagdata);
	}

	public function length()
	{
		return strlen($this->EE->TMPL->tagdata);
	}

	public function encode()
	{
		return htmlentities($this->EE->TMPL->tagdata);
	}

	public function decode()
	{
		return html_entity_decode($this->EE->TMPL->tagdata);
	}

	public function trim()
	{
		$direction = $this->EE->TMPL->fetch_param('direction', 'both');

		switch ($direction)
		{
			case 'both':
				return trim($this->EE->TMPL->tagdata);
			case 'left':
				return ltrim($this->EE->TMPL->tagdata);
				break;
			default:
				return rtrim($this->EE->TMPL->tagdata);
				break;
		}
	}

	public function fixed()
	{
		$length = (int) $this->EE->TMPL->fetch_param('length', 100);
		$character = $this->EE->TMPL->fetch_param('character', ' ');

		switch ($this->EE->TMPL->fetch_param('direction', 'right'))
		{
			case 'right':
				$type = STR_PAD_RIGHT;
				break;
			case 'left':
				$type = STR_PAD_LEFT;
				break;
			default:
				$type = STR_PAD_BOTH;
		}

		return str_pad($this->EE->TMPL->tagdata, $length, $character, $type);
	}

	function usage()
	{
		ob_start();
?>
Usage:

{exp:streeng:capitalize}test string{/exp:streeng:capitalize} = Test string
{exp:streeng:camel}test string{/exp:streeng:camel} = Test String
{exp:streeng:lower}Test String{/exp:streeng:lower} = test string
{exp:streeng:upper}Test String{/exp:streeng:upper} = TEST STRING
{exp:streeng:slug}Test String{/exp:streeng:slug} = test-string
{exp:streeng:truncate length="7" append="..."}Test String{/exp:streeng:truncate} = Test St...
{exp:streeng:replace find="Test" append="New"}Test String{/exp:streeng:replace} = New String
{exp:streeng:trim direction="left"}  Test String{/exp:streeng:trim} = Test String (direction - right|left|both)
{exp:streeng:strip allow="<span><a>"}  <div><span><a href="#">Test String</a></span></div>{/exp:streeng:trim} = <span><a href="#">Test String</a></span>
{exp:streeng:length}Test String{/exp:streeng:length} = 11
{exp:streeng:encode}Test & String{/exp:streeng:encode} = Test &amp; String
{exp:streeng:decode}Test &amp; String{/exp:streeng:decode} = Test & String
{exp:streeng:fixed length="15" character="*" direction="right"}Test String{/exp:streeng:fixed} = Test String**** (direction - right|left|both)
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}
?>