<?php
use Client\Client;

interface MessageComponent extends Component
{
	function onMessage($message, Client $client);	
}