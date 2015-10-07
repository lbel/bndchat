<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class ConnectPacket extends AbstractPacket
{
  private static $logger;
  private $message;
  private $out;

  public static function init()
  {
    self::$logger = LogFactory::get("ConnectPacket");
  }

  public function process() 
  {
    $this->out = json_encode(array("type" => "connect", "name" => $this->client->getName(), "ip" => $this->client->getIp(), "color" => $this->client->getColor()));
    $this->handler->outToAll($this);

    self::$logger->info("Client " . $this->client->getIP() . " connected");

    return true;
  }

  public function fromArray($array)
  {
    return false;
  }

  public function toJSON() 
  {
    return $this->out;
  }
}

ConnectPacket::init();