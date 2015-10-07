<?php
namespace Client\Dao;

use \Server;
use \Client\Client;

class LocalClientStorage implements ClientStorage
{
  /**
   *
   * @var Server
   */
  protected $server;

  /**
   *
   * @var Clients[]
   */
  protected $clients;

  protected $sockets;

  public function __construct(Server $server)
  {
    $this->server = $server;
    $this->clients = array();
    $this->sockets = array();
  }

  public function setListenSocket($listenSocket)
  {
    $this->sockets[Client::socketHash($listenSocket)] = $listenSocket;
  }

  public function addClient(Client $client)
  {
    $hash = $client->getHash();
    $this->clients[$hash] = $client;
    $this->sockets[$hash] = $client->getSocket();
  }

  public function removeClient(Client $client)
  {
    $hash = $client->getHash();
    unset($this->clients[$hash]);
    unset($this->sockets[$hash]);
  }

  /**
   * Returns false if the socket is not found.
   *
   * @param resource $socket         
   * @return Client|boolean
   */
  public function getBySocket($socket)
  {
    $hash = Client::socketHash($socket);
    
    if (isset($this->clients[$hash])) {
      return $this->clients[$hash];
    }

    return false;
  }

  /**
   * Returns false if the name is not found.
   *
   * @param string $name         
   * @return Client|boolean
   */
  public function getByName($name)
  {
    foreach ($this->clients as $client) {
      if ($client->getName() == $name) {
        return $client;
      }
    }

    return false;
  }

  /**
   * Returns false if the IP is not found.
   *
   * @param string $ip            
   * @return Clients|boolean
   */
  public function getByIP($ip)
  {
    foreach ($this->clients as $client) {
      if ($client->getIP() == $ip) {
        return array($client);
      }
    }

    return false;
  }

  public function getSockets()
  {
    return $this->sockets;
  }

  public function rewind()
  {
    return reset($this->clients);
  }

  public function current() 
  {
    return current($this->clients);
  }

  public function key() 
  {
    return key($this->clients);
  }

  public function next() 
  {
    return next($this->clients);
  }

  public function valid() 
  {
    $key = key($this->clients);
    return $key !== null && $key !== false;
  }
}