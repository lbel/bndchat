<?php
namespace Log;

/**
 * Simple logging interface. 
 * 
 * TODO: Write an implementation.
 */
interface Logger
{
	/**
	 * 
	 * @param string $text
	 * @param \LogLevel $level
	 */
	function sendMessage( $text, $level );

	function info( $text );
	function warning( $text );
	function error( $text );
	function trace( $text );
	function fatal( $text );
}