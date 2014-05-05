# 1.5.1

2014-05-05

- Resolved issue where appended parameter was applied regardless of actual truncation

# 1.5.0

2014-04-26

- HTML tags are now excluded when doing character or word counts
- More reliable truncation and tag completion when HTML is present
- Removed the autoclose parameter, depending on the allowed parameter it is automatically handled
- Removed references to CodeIgniter string and text helpers, making Streeng fully self-enclosed

# 1.4.2

2014-01-27

- Fixed issue with non-escaped prefix or suffix forward slashes

# 1.4.0

2013-11-19

- Resolved issue with DOMDocument and auto-closing tags
- Replaced mode parameter with autoclose parameter including false option to disable auto-close

# 1.3.1

2013-10-27

- Updated close tags function to use tidy when available

# 1.3.0

2013-10-25

- Added tag auto-close capability when truncating
- Added new options to get substring and split counts

# 1.2.0

2013-09-26

- Added new url parameter to URL encode given string
- Updated to use the new ee() global function (EE 2.6 required)

# 1.1.0

2013-07-20

- Added case sensitivity parameter (thanks mark-cr)
- Added ability to return 0/1 if a substring is found in a string

# 1.0.2

2013-05-30

- EE <2.6 compatability

# 1.0.1

2013-05-17

- Complete overhaul

# 1.0.0

2013-05-15

- Initial commit