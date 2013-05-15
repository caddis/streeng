ExpressionEngine String Operations
====

Perform common operations on strings in ExpressionEngine.

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