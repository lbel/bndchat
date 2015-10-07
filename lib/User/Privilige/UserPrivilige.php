<?php
namespace User\Privilige;

class UserPrivilige
{
	private $talkRights = false;
	private $floodRights = false;
	private $htmlRights = false;
	private $imgRights = false;
	private $kickRights = false;
	private $banRights = false;

	public function __construct($talk, $flood, $img, $html, $kick, $ban)
	{
		$this->talkRights = (bool)$talk;
		$this->floodRights = (bool)$flood;
		$this->imgRights = (bool)$img;
		$this->htmlRights = (bool)$html;
		$this->kickRights = (bool)$kick;
		$this->banRights = (bool)$ban;
	}

	public function canHtml()
	{
		return $this->htmlRights;
	}

	public function canKick()
	{
		return $this->kickRights;
	}

	public function canBan()
	{
		return $this->banRights;
	}

	public function canImg()
	{
		return $this->imgRights;
	}
}