<?php
namespace Client\Dao;

use \Client\Client;

/**
 * Abstract container for clients. 
 * 
 * Usually implemented by an in-memory approach, with a logging
 * variant. 
 */
interface ClientStorage extends \Iterator
{
    /**
     * Adds client to collection.
     * 
     * @param Client $client
     * @return void
     */
    function addClient(Client $client);
    
    /**
     * Removes client from collection.
     * 
     * @param Client $client
     * @return void
     */
    function removeClient(Client $client);
    
    /**
     * When found, the client for this socket is returned. 
     * 
     * When not found, false is returned. 
     * 
     * @param resource $socket
     * @return Client
     */
    function getBySocket($socket);
    
    /**
     * 
     * When found, the client for this name is returned. 
     * 
     * When not found, false is returned. 
     * 
     * @param string $name
     * 
     * @return Client
     */
    function getByName($name);
    
    /**
     * When found, a list of clients for this IP address is returned. 
     * 
     * When not found, false is returned. 
     * 
     * @param Client[] $ip
     */
    function getByIp($ip);
    
    function setListenSocket($listenSocket);
    
    /**
     * Returns an array of sockets stored. 
     * 
     * @return mixed[]
     */
    function getSockets();
}