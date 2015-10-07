<?php
namespace Configuration;

use User\Dao\LocalUserDao;

class Configuration
{
	/**
	 * 
	 * @var int
	 */
	private $port;
	
	public function getPort()
	{
		return $this->port;	
	}
	
	public function setPort( $port )
	{
		$this->port = $port;
		
		return $this;
	}
	
	public function getUserDao()
	{
		// only to be used once...
		return new LocalUserDao();
	}
}