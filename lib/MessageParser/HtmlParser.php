<?php
namespace MessageParser;

/**
 * Does a htmlspecialchars.
 */
class HtmlParser implements MessageParser
{
	public function process( $string )
	{
		return htmlspecialchars( $string );	
	}
}