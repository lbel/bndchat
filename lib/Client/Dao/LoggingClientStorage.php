<?php
namespace Client\Dao;

use Client\Client;
use \Log\Logger;
use \Log\LogLevel;
use Log\LogFactory;

/**
 * Objects of this type catch all calls and call the logger
 * to inform of any (incorrect) calls.
 * 
 * Expected behaviour is written as TRACE, while possibly
 * unexpected behaviour is logged as INFO.
 * 
 */
class LoggingClientStorage implements ClientStorage
{
	/**
	 * @var ClientCollection
	 */
	private $motherCollection;
	
	/**
	 * @var \Log\Logger
	 */
	private static $logger = null;
	
	public function __construct(ClientStorage $motherCollection)
	{
		$this->motherCollection = $motherCollection;
		
		if (static::$logger === null)
			self::$logger = LogFactory::get("LoggingClientStorage");
	}
	
	public function addClient(Client $client)
	{
		static::$logger->sendMessage("Received call to addClient", LogLevel::TRACE);
		$return = $this->motherCollection->addClient($client);
		static::$logger->sendMessage("Finished call to addClient", LogLevel::TRACE);
		
		return $return;
	}
	
	public function removeClient(Client $client)
	{
		static::$logger->sendMessage("Received call to removeClient", LogLevel::TRACE);
		$return = $this->motherCollection->removeClient($client);
		static::$logger->sendMessage("Finished call to removeClient", LogLevel::TRACE);
		
		return $return;
	}
	
	public function getBySocket($socket)
	{
		static::$logger->sendMessage("Received call to getBySocket " . $socket, LogLevel::TRACE);
		$return = $this->motherCollection->getBySocket($socket);
		static::$logger->sendMessage("Finished call to getBySocket", LogLevel::TRACE);
		
		return $return;
	}
	
	public function getByName($name)
	{
		static::$logger->sendMessage("Received call to getByName " . $name, LogLevel::TRACE);
		$return = $this->motherCollection->getByName($name);
		static::$logger->sendMessage("Finished call to getByName", LogLevel::TRACE);
		
		return $return;
	}
	
	public function getByIp($ip)
	{
		static::$logger->sendMessage("Received call to getByIp " . $ip, LogLevel::TRACE);
		$return = $this->motherCollection->getByIp($ip);
		static::$logger->sendMessage("Finished call to getByIp", LogLevel::TRACE);
		
		return $return;
	}
	
	public function setListenSocket($listenSocket)
	{
		
		static::$logger->sendMessage("Received call to setListenSocket", LogLevel::TRACE);
		
		$return = $this->motherCollection->setListenSocket($listenSocket);
		static::$logger->sendMessage("Finished call to setListenSocket", LogLevel::TRACE);
		
		return $return;
	}
	
	public function getSockets()
	{
		// this would be a bit too much
		//static::$logger->sendMessage("Received call to getSockets", LogLevel::TRACE);
		$return = $this->motherCollection->getSockets();
		//static::$logger->sendMessage("Finished call to getSockets", LogLevel::TRACE);
		
		return $return;
	}

	public function rewind()
  {
    return $this->motherCollection->rewind();
  }

  public function current() 
  {
    return $this->motherCollection->current();
  }

  public function key() 
  {
    return $this->motherCollection->key();
  }

  public function next() 
  {
    return $this->motherCollection->next();
  }

  public function valid() 
  {
    return $this->motherCollection->valid();
  }
};