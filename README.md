# Streeng 1.4.2

Perform common operations on strings in ExpressionEngine. All parameters are optional.

## Parameters

	allowed="p|span|a" - pass "none" to strip all tags or a | delimited list of allowed tags (defaults = allow al)
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

## Usage

```html
{exp:streeng allowed="p" title="yes" repeat="2" find=" a " replace=" my "}  <p><b>This</b> is a <a href="#">test</a>.</p>{/exp:streeng}

<p>This Is My Test.</p>
<p>This Is My Test.</p>
<p>This Is My Test.</p>

{if "{exp:streeng find='this' insensitive='yes'}This is a test string{/exp:streeng}"}
	We found 'this' in 'This is a test string'!
{/if}
```

## License

Copyright 2014 Caddis Interactive, LLC

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.