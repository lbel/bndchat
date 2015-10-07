<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class WhoisPacket extends AbstractPacket
{
  private static $logger;
  private $init;
  private $message;
  private $out;

  public static function init()
  {
    self::$logger = LogFactory::get("MessagePacket");
  }

  public function process() 
  {
    if (!$this->init) {
      return false;
    }

    $this->out = json_encode(array("type" => "message", "user" => $this->client->getName(), "time" => date("H:i:s"), "message" => nl2br(htmlspecialchars(EmojiParser::process($this->message)))));
    // TODO: stringparser

    $this->handler->outToAll($this);

    return true;
  }

  public function fromArray($array)
  {
    $this->init = true;
    if (!isset($array['message']) || empty($array['message'])) {
      // TODO: exception gooien
      self::$logger->warning("No message");
      return false;
    }
    $this->message = $array['message'];

    return $this->process();
  }

  public function toJSON() 
  {
    return $this->out;
  }
}

WhoisPacket::init();