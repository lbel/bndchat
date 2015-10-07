<?php
use Client\Client;

interface Component
{
	function onConnect(Client $client);
	
	function onClose(Client $client);
	
	function onError(Client $client);
}