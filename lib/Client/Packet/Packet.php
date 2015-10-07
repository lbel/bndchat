<?php
namespace Client\Packet;

use Client\Client;

interface Packet
{
	function setClient(Client $client);
	function process();
	function fromArray($array);
	function toJSON();	
}