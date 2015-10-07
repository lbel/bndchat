<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class ColorPacket extends AbstractPacket
{
  private static $logger;
  private $init;
  private $color;
  private $out;

  public static function init()
  {
    self::$logger = LogFactory::get("ColorPacket");
  }

  public function process() 
  {
    if (!$this->init) {
      return false;
    }

    if (!preg_match("/^\#[0-9a-f]{3,6}$/i", $this->color)) {
      $this->out = json_encode(array("type" => "system", "message" => "Invalid color"));
      $this->handler->out($this->client, $this);
      return false;
    }

    foreach ($this->handler->getServer()->getClients() as $client) {
      if ($client->getColor() == $this->color) {
        $this->out = json_encode(array("type" => "system", "message" => "Color already exists"));
        $this->handler->out($this->client, $this);
        return false;
      }
    }

    $this->client->setColor($this->color);

    $this->out = json_encode(array("type" => "color", "user" => $this->client->getName(), "color" => $this->color));

    $this->handler->outToAll($this);

    return true;
  }

  public function fromArray($array)
  {
    $this->init = true;
    if (!isset($array['color']) || empty($array['color'])) {
      // TODO: exception gooien
      self::$logger->warning("No new color");
      return false;
    }

    $this->color = $array['color'];

    return $this->process();
  }

  public function toJSON() 
  {
    return $this->out;
  }
}

ColorPacket::init();