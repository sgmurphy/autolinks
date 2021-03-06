<?php
/**
 * Replace URLs Callback
 *
 * Replace all URLs found in string with the
 * result of a callback function.
 *
 * If the $urls argument is supplied it will be filled with all found URLs.
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @copyright Sean Murphy 2008-2011
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @param string $text
 * @param mixed $callback
 * @param array $urls
 * @return string
 */
function replace_urls_callback($text, $callback, &$urls = array()) {
	// Start off with a regex
	$regex = '#
	(?:
		(?:
			(?:https?|ftps?|mms|rtsp|gopher|news|nntp|telnet|wais|file|prospero|webcal|xmpp|irc)://
			|
			(?:mailto|aim|tel):
		)
		[^.\s]+\.[^\s]+
		|
		(?:[^.\s/:]+\.)+
		(?:museum|travel|[a-z]{2,4})
		(?:[:/][^\s]*)?
	)
	#ix';
	preg_match_all($regex, $text, $matches);
	
	$index = 0; // Gets passed to callback as a valid URL's index
	$offset = 0; // Track position in string as it's processed
	
	// Then clean up what the regex left behind
	foreach($matches[0] as $orig_url) {
		$url = htmlspecialchars_decode($orig_url);
		
		// Make sure we didn't pick up an email address
		if (preg_match('#^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$#i', $url)) continue;
		
		// Remove surrounding punctuation
		$url = trim($url, '.?!,;:\'"`([<');
		
		// Remove surrounding parens and the like
		preg_match('/[)\]>]+$/', $url, $trailing);
		if (isset($trailing[0])) {
			preg_match_all('/[(\[<]/', $url, $opened);
			preg_match_all('/[)\]>]/', $url, $closed);
			$unopened = count($closed[0]) - count($opened[0]);
		    
			// Make sure not to take off more closing parens than there are at the end
			$unopened = ($unopened > mb_strlen($trailing[0])) ? mb_strlen($trailing[0]):$unopened;
		    
			$url = ($unopened > 0) ? mb_substr($url, 0, $unopened * -1):$url;
		}
		
		// Remove trailing punctuation again (in case there were some inside parens)
		$url = rtrim($url, '.?!,;:\'"`');
		
		// Make sure we didn't capture part of the next sentence
		preg_match('#((?:[^.\s/]+\.)+)(museum|travel|[a-z]{2,4})#i', $url, $url_parts);
		
		// Were the parts capitalized any?
		$last_part = (mb_strtolower($url_parts[2]) !== $url_parts[2]) ? true:false;
		$prev_part = (mb_strtolower($url_parts[1]) !== $url_parts[1]) ? true:false;
		
		// If the first part wasn't cap'd but the last part was, we captured too much
		if ((!$prev_part && $last_part)) {
			$url = mb_substr($url, 0 , mb_strpos($url, '.'.$url_parts['2'], 0));
		}
		
		// Capture the new TLD
		preg_match('#((?:[^.\s/]+\.)+)(museum|travel|[a-z]{2,4})#i', $url, $url_parts);
		
		$tlds = array('ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw');
		
		if (!in_array($url_parts[2], $tlds)) continue;
		
		// Put the url back the way we found it.
		$url = (mb_strpos($orig_url, htmlspecialchars($url)) === FALSE) ? $url:htmlspecialchars($url);
		
		// Add URL to array
		$urls[] = $url;
		
		// Call user specified function
		$modified_url = is_callable($callback) ? call_user_func($callback, $url, $index):$url;
		
		// Replace it!
		$start = mb_strpos($text, $url, $offset);
		$text = mb_substr($text, 0, $start).$modified_url.mb_substr($text, $start + mb_strlen($url), mb_strlen($text));
		$offset = $start + mb_strlen($modified_url);
		
		$index++;
	}
	
	return $text;
}

/**
 * Linkify
 *
 * Turn a URL into a clickable link
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @param string $url
 * @return string
 */
function linkify($url) {
	$display = $url;
	$url = (!preg_match('#^([a-z]+://|(mailto|aim|tel):)#i', $url)) ? 'http://'.$url:$url;
	return "<a href=\"$url\" class=\"extlink\">$display</a>";
}

/**
 * Markdown Linkify
 *
 * Turn a URL into a clickable link using Markdown markup.
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @param string $url
 * @return string
 */
function markdown_linkify($url) {
	$display = $url;
	$url = (!preg_match('#^([a-z]+://|(mailto|aim|tel):)#i', $url)) ? 'http://'.$url:$url;
	return "[$display]($url)";
}

/**
 * Replace Email Addresses Callback
 *
 * Replace all email addresses found in string with the
 * result of a callback function.
 *
 * If the $emails argument is supplied it will be filled with all found email addresses.
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @copyright Sean Murphy 2010-2011
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @param string $text
 * @param mixed $callback
 * @param array $emails
 * @return string
 */
function replace_emails_callback($text, $callback, &$emails = array()) {
	preg_match_all('#\b([A-Z0-9._%+-]+)@([A-Z0-9.-]+\.[A-Z]{2,4})\b#i', $text, $matches);
	
	$offset = 0;
	foreach ($matches[0] as $index => $email) {
		// Add email to array
		$emails[] = $email;
		
		// Call user specified function
		$modified_email = is_callable($callback) ? call_user_func($callback, $matches[1][$index], $matches[2][$index], $index):$email;
		
		// Replace it!
		$start = mb_strpos($text, $email, $offset);
		$text = mb_substr($text, 0, $start).$modified_email.mb_substr($text, $start + mb_strlen($email), mb_strlen($text));
		$offset = $start + mb_strlen($modified_email);
	}
	
	return $text;
}

/**
 * Linkify Email
 *
 * Turn an email into a clickable link
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @param string $user
 * @param string $host
 * @return string
 */
function linkify_email($user, $host) {
	$email = $user.'@'.$host;
	return "<a href=\"mailto:$email\" class=\"extlink\">$email</a>";
}
?>