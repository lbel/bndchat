<?php
namespace Client\Packet;

use Log\LogFactory;
use Client\PacketHandler;
use Client\Packet\Packet;

class DisconnectPacket extends AbstractPacket
{
  const DISCONNECT_QUIT = 1;
  const DISCONNECT_TIMEOUT = 2;

  private static $logger;
  private $disconnectType;
  private $message;
  private $out;

  public function __construct(PacketHandler $handler, $disconnectType = self::DISCONNECT_TIMEOUT) 
  {
    parent::__construct($handler);
    $this->disconnectType = $disconnectType;
  }

  public static function init()
  {
    self::$logger = LogFactory::get("DisconnectPacket");
  }

  public function process() 
  {
    $disconnectType = $this->disconnectType == self::DISCONNECT_QUIT ? "quit" : "timeout";

    $this->out = json_encode(array("type" => "disconnect", "name" => $this->client->getName(), "disconnect_type" => $disconnectType));
    
    foreach ($this->handler->getServer()->getClients() as $client)
    {
      if ($client->getHash() == $this->client->getHash()) {
        continue;
      }
      $this->handler->out($client, $this);
      
      self::$logger->trace("Sending disconnect information packet to " . $client->getIP() . ", for disconnecting user " . $this->client->getName());
    }

    self::$logger->info("Client " . $client->getIP() . " disconnected (".$disconnectType.")");
    
    $this->handler->getServer()->getClients()->removeClient( $this->client );
    socket_close($this->client->getSocket());
    unset ($this->client);
    
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

DisconnectPacket::init();