<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class UsersPacket extends AbstractPacket
{
  private static $logger;
  private $message;
  private $out;

  public static function init()
  {
    self::$logger = LogFactory::get("UsersPacket");
  }

  public function process() 
  {
    $users = array();
    foreach ($this->handler->getServer()->getClients() as $client) {
      $users[] = array("name" => $client->getName(), "color" => $client->getColor());
    }

    $this->out = json_encode(array("type" => "users", "users" => $users));
    $this->handler->out($this->client, $this);

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

UsersPacket::init();