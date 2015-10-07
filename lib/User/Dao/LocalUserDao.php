<?php
namespace User\Dao;

use User\User;
class LocalUserDao implements UserDao
{
	/**
	 * Internal userlist.
	 * 
	 * @var User[]
	 */
	private $userList;
	
	/**
	 * map<string, User>
	 */
	private $usernameToUserMap;
	
	public function __construct()
	{
		$this->userList = array();
		$this->usernameToUserMap = array();
	}
	
	function getByUsername( $username )
	{
		return $this->usernameToUserMap[ $username ];
	}
	
	function getById( $userId )
	{
		return $this->getByIdList( array($userId) );
	}
	
	function getByIdList( $userIdList )
	{
		$returnList = array();
		
		foreach ($userIdList as $userId)
		{
			if (isset($this->userList[ $userId ]))
				$returnList[ $userId ] = $this->userList[ $userId ];	
		}
		
		return $returnList;
	}
	
	function getAll()
	{
		return $userList;
	}
	
	function push(User $user)
	{
		$this->userList[ $user->getI] = $user;
	}
	
	public function remove(User $user)
	{
		unset($this->userList[ $userId ]);
		unset($this->usernameToUserMap[ $user->getName() ]);
	}
}