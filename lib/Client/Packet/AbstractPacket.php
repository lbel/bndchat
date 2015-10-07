<?php
namespace Client\Packet;

use Client\Client;
use Client\PacketHandler;

/**
 * A packet sets up all data that in the end can be send to the user. 
 * 
 * The packet is sent out upon an explicit call to process. 
 * 
 * @author ldufour
 */
abstract class AbstractPacket implements Packet
{
  protected $handler;
  protected $client;

  public function __construct(PacketHandler $handler)
  {
    $this->handler = $handler;
  }

  public function setClient(Client $client) {
    $this->client = $client;
  }

  abstract public function process();
  abstract public function fromArray($array);
  abstract public function toJSON();
}