<?php
namespace Configuration;

use Log\LogFactory;

class JsonConfigurationReader implements ConfigurationReader
{
	/**
	 * @var \Log\Logger
	 */
	private static $logger = null;

	public function __construct()
	{
		if (self::$logger === null)
			self::$logger = LogFactory::get("JsonConfigurationReader");
	}

	public function getConfiguration( $name )
	{
		if (! file_exists($name)) {
			self::$logger->fatal("Tried to open file " . $name . ", but without success.");
			throw new FileNotFoundException("Configuration file '" . $name . "' not found");
		}
		
		$file = file_get_contents($name);
		$json = json_decode($file, true);
		
		if ($json === null) {
			self::$logger->fatal("Tried to decode json file " . $name . ", but without success.");
			throw new Exception("Configuration file '" . $name . "' cannot be decoded");
		}
		
		$configuration = new Configuration();
		$configuration->setPort($json['port']);
		return $configuration;
	}
}