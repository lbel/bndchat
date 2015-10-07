<?php
namespace User\Dao;

use User\User;

interface UserDao
{
	/**
	 * 
	 * @param string $username
	 * @return User
	 */
	function getByUsername( $username );
	
	/**
	 * 
	 * @param int $userId
	 */
	function getById( $userId );
	
	/**
	 * Returns a map based on ID -> User.
	 * 
	 * @param string[] $userIdList
	 * @return map<string, User>
	 */
	function getByIdList( $userIdList );
	
	/**
	 * @return User[]
	 */
	function getAll();
	
	/**
	 * Adds a user to the internal storage.
	 * 
	 * @param User $user
	 */
	function push(User $user);
	
	function remove (User $user);
}