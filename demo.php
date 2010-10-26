<?php
if (isset($_GET['download'])) {
	header('Content-Type: text/plain', true);
	echo file_get_contents(dirname(__FILE__).'/autolinks.php');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Auto-Linking URLs Demo</title>
	
	<style type="text/css">
		body {
			padding: 50px 75px;
		}
		
		ul {
			margin: 0;
			padding: 0;
			background: #F9FF90;
			overflow: hidden;
		}
		
		li {
			list-style: none;
			margin: 0 10px;
			padding: 5px;
			float: left;
		}
	</style>
</head>
<body>
	
<h1>Auto-Linking URLs Demo</h1>

<ul>
	<li class="first"><a href="?download">Download the source</a></li>
	<li><a href="http://iamseanmurphy.com/2008/12/01/auto-linking-urls-with-php/">Read the blog post</a></li>
</ul>

<pre>
<?php
$text = <<<DEL
example
http://example
http://example/
http://example/path

http://example.com
https://example.com
ftp://example.com
ftps://example.com
http://user@example.com
http://user:pass@example.com
http://example.com:8080
http://www.example.com

http://example.com/
http://example.com/path
http://example.com/path.html
http://example.com/path.html#fragment
http://example.com/path.php?foo=bar&bar=foo

http://müllärör.de
http://ﺱﺲﺷ.com
http://сделаткартинки.com
http://tūdaliņ.lv
http://brændendekærlighed.com
http://あーるいん.com
http://예비교사.com

http://example.com.
http://example.com?
http://example.com!
http://example.com,
http://example.com;
http://example.com:

'http://example.com'
"http://example.com"
`http://example.com`

(http://example.com)
[http://example.com]
<http://example.com>

http://example.com/path/(foo)/bar
http://example.com/path/[foo]/bar
http://example.com/path/foo/(bar)
http://example.com/path/foo/[bar]

Hey, check out my cool site http://example.com okay?
What about parens (e.g. http://example.com/path/foo/(bar))?
What about parens (e.g. http://example.com/path/foo/(bar)?
What about parens (e.g. http://example.com/path/foo/(bar).)?
What about parens (e.g. http://example.com/path/(foo,bar)?

Unbalanced too (e.g. http://example.com/path/((((foo)/bar)?
Unbalanced too (e.g. http://example.com/path/(foo))))/bar)?
Unbalanced too (e.g. http://example.com/path/foo/((((bar)?
Unbalanced too (e.g. http://example.com/path/foo/(bar))))?

example.com
example.org
example.co.uk
www.example.co.uk
farm1.images.example.co.uk
example.museum
example.travel

example.com.
example.com?
example.com!
example.com,
example.com;
example.com:

'example.com'
"example.com"
`example.com`

(example.com)
[example.com]
<example.com>

Hey, check out my cool site example.com okay?
Hey, check out my cool site example.com.I made it.
Hey, check out my cool site example.com.Funny thing...
Hey, check out my cool site example.com.You will love it.

What about parens (e.g. example.com/path/foo/(bar))?
What about parens (e.g. example.com/path/foo/(bar)?
What about parens (e.g. example.com/path/foo/(bar).)?
What about parens (e.g. example.com/path/(foo,bar)?

file.ext
file.html
file.php
DEL;

$text2 = <<<DEL
[Coming soon: IDNA TLDs]
http://例子.测试
http://उदाहरण.परीक्षा
http://пример.испытание
http://실례.테스트
http://בײַשפּיל.טעסט
http://例子.測試
http://مثال.آزمایشی
http://உதாரணம்.பரிட்சை
http://παράδειγμα.δοκιμή
http://مثال.إختبار
http://例え.テスト
DEL;

mb_internal_encoding('UTF-8');

include_once(dirname(__FILE__).'/autolinks.php');

echo replace_urls_callback($text, 'linkify');
?>
</pre>

</body>
</html>
