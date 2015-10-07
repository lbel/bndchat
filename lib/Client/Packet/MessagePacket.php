<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;
use MessageParser\EmojiParser;
use MessageParser\PriviligeAware\PriviligeAwareMessageParser;
use User\Privilige\UserPrivilige;

class MessagePacket extends AbstractPacket
{

	private static $logger;

	private static $priviligeAwareMessageParser;

	private $init;

	private $message;

	private $out;

	public static function init()
	{
		self::$logger = LogFactory::get("MessagePacket");
		self::$priviligeAwareMessageParser = new PriviligeAwareMessageParser();
	}

	public function process()
	{
		if (! $this->init) {
			return false;
		}
		
		$parsedMessage = $this->message;
		$parsedMessage = self::$priviligeAwareMessageParser->process($parsedMessage, new UserPrivilige(true, true, false, false, false, false));
		
		// wat is dit voor iets terribads
		$this->out = json_encode(array(
			"type" => "message",
			"user" => $this->client->getName(),
			"time" => date("H:i:s"),
			"message" => nl2br($parsedMessage)
		));
		// TODO: stringparser
		
		$this->handler->outToAll($this);
		
		return true;
	}

	public function fromArray($array)
	{
		$this->init = true;
		if (! isset($array['message']) || empty($array['message'])) {
			// TODO: exception gooien
			self::$logger->warning("No message");
			return false;
		}
		$this->message = $array['message'];
		
		// return $this->process();
	}

	public function toJSON()
	{
		return $this->out;
	}
}

MessagePacket::init();