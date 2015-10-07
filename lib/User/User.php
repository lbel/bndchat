<?php
namespace User;

use User\Privilige\UserPrivilige;

class User implements \Named
{
	/**
	 * @var string
	 */
	private $userId;
	
	/**
	 *
	 * @var string
	 */
	private $name;

	/**
	 *
	 * @var bool
	 */
	private $connected = false;

	/**
	 *
	 * @var UserPrivilige
	 */
	private $userPriviliges;

	public function __construct( $id, $name )
	{
		$this->userId = $id;
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getPriviliges()
	{
		return $this->userPriviliges;
	}
	
	public function getId()
	{
		return $this->userId;	
	}
	
	public function setPriviliges( UserPrivilige $userPrivilige)
	{
		$this->userPriviliges = $userPrivilige;
	}
}
