<?php
namespace MessageParser;

class UrlParser implements MessageParser
{
	public function __construct()
	{
		
	}

	/**
	 * Code van het internet geript.
	 **/
	public function process( $string )
	{
		$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		$callback = create_function('$matches', '
       $url       = array_shift($matches);
       $url_parts = parse_url($url);
		
       $text = parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
       $text = preg_replace("/^https?:\/\/www./", "", $text);
		
       $last = -(strlen(strrchr($text, "/"))) + 1;
       if ($last < 0) {
           $text = substr($text, 0, $last) . "&hellip;";
       }
		
       return sprintf(\'<a rel="nowfollow" href="%s">%s</a>\', $url, $text);
   ');	


		$parsed = preg_replace_callback($pattern, $callback, $string);
		
		return $parsed;
	}
}