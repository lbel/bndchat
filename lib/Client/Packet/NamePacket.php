<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class NamePacket extends AbstractPacket
{
  private static $logger;
  private $init;
  private $name;
  private $out;

  public static function init()
  {
    self::$logger = LogFactory::get("NamePacket");
  }

  public function process() 
  {
    if (!$this->init) {
      return false;
    }
    
    if ($this->client->getName() == $this->name)
    	return false;

    foreach ($this->handler->getServer()->getClients() as $client) {
      if ($client->getName() == $this->name) {
        $this->out = json_encode(array("type" => "system", "message" => "Name already exists"));
        $this->handler->out($this->client, $this);
        return false;
      }
    }

    $oldName = $this->client->getName();
    $this->client->setName($this->name);

    $this->out = json_encode(array("type" => "name", "user_old" => $oldName, "user_new" => $this->name));

    $this->handler->outToAll($this);

    return true;
  }

  public function fromArray($array)
  {
    $this->init = true;
    if (!isset($array['name']) || empty($array['name'])) {
      // TODO: exception gooien
      self::$logger->warning("No new name");
      return false;
    }
    $this->name = $array['name'];
  }

  public function toJSON() 
  {
    return $this->out;
  }
}

NamePacket::init();