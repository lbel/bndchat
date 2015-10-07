<?php
namespace User;

use Client\Client;

use User\Dao\UserDao;

class UserComponent implements \Component
{
	/**
	 * @var UserDao
	 */
	private $userStorage;
	
	public function __construct(UserDao $userDao)
	{
		$this->userStorage = $userDao;
	}
	
	public function onConnect(Client $client)
	{
		$newUser = new User( $username, $username );
		
		$client->setUser($newUser);
		
		$this->userStorage->push($newUser);
	}
	
	public function onMessage($message, Client $client)
	{
		
	}
	
	public function onClose(Client $client)
	{
		$this->userStorage->remove($client->getUser());
		$client->setUser(null);
	}
	
	public function onError(Client $client)
	{
		
	}
}
