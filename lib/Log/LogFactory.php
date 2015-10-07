<?php
namespace Log;

class LogFactory
{
	const DEFAULT_NAMED_LOGGER = "\Log\ConsoleLogger";
	
	/**
	 * @var LogFactory
	 */
	private static $instance;
	
	public static function getInstance()	
	{
		if (self::$instance === null)
			self::$instance = new LogFactory();
		
		return self::$instance;
	}
	
	/**
	 * @param string $name
	 * @return Logger
	 */
	public static function get( $name )
	{
		return self::getInstance()->getLogger( $name );
	}

	private function __construct()
	{
		
	}
	
	/**
	 * @param string $name
	 * @return Logger
	 */
	public function getLogger( $name )
	{
		$loggerName = LogFactory::DEFAULT_NAMED_LOGGER;
		return new $loggerName( $name );
	}
}