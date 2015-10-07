<?php
namespace Configuration;

interface ConfigurationReader
{
	/**
	 * @param string $name filename / database etc.
	 * @reutrn Configuration
	 */
	function getConfiguration( $name );	
}