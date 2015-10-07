<?php
namespace MessageParser;

/**
 * Objects of this type are responsible for parsing strings. 
 * 
 * The responsibility of whether or not this object should process
 * the string based on e.g. UserPriliges is not handled by these objects,
 * which is why process is not aware of the UserPrivilige object.
 * 
 * A classic example is the Urlparser ({@see UrlParser}) or the HtmlParser({@see HtmlParser}).
 */
interface MessageParser
{
	/**
	 * Applies the operation to a copy of the given string, but does not 
	 * alter the given string itself.
	 * 
	 * @param string $string
	 * @return string parsed string
	 */
	function process( $string );
}