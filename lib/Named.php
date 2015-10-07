<?php
/**
 * Objects carrying a name, which is not in all cases unique (but in some it is).
 *
 */
interface Named
{
	/**
	 * @return string name
	 */
	function getName();	
}