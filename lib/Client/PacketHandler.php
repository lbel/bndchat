<?php
namespace Client;

use Log\LogFactory;
use Client\Client;
use Client\Packet\Packet;
use Client\Packet\MessagePacket;
use Client\Packet\DisconnectPacket;
use Client\Packet\NamePacket;
use Client\Packet\ColorPacket;
use Log\Logger;

class PacketHandler
{

	const PACKET_TYPE_CLOSE = "close";

	const PACKET_TYPE_MESSAGE = "message";

	const PACKET_TYPE_PING = "ping";

	const PACKET_TYPE_BINARY = "binary";

	const PACKET_TYPE_PONG = "pong";

	/**
	 *
	 * @var \Server
	 */
	protected $server;

	/**
	 *
	 * @var Logger
	 */
	private $logger;

	public function __construct(\Server $server)
	{
		$this->server = $server;
		$this->logger = LogFactory::get("PacketHandler");
	}

	public function getServer()
	{
		return $this->server;
	}

	private function handleMessagePacket(Client $from, $raw, $json)
	{
		if ($json === false) { // I think json replies null and not false.
		                       // JSON decode failed
			$this->logger->warning("Unable to decode JSON for incoming packet from " . $from->getIp());
			return false;
		}
		
		if (! isset($json['type'])) {
			$this->logger->warning("Invalid message packet from " . $from->getIp());
			var_dump($raw);
			
			return false;
		}
		
		$packet = false;
		switch ($json['type']) {
			case "message":
				$this->logger->trace("Got message packet from " . $from->getIp() . ", " . $from->getName());
				$packet = new MessagePacket($this);
				break;
			case "disconnect":
				$this->logger->trace("Got disconnect packet from " . $from->getIp() . ", " . $from->getName());
				$packet = new DisconnectPacket($this, DisconnectPacket::DISCONNECT_QUIT);
				break;
			case "name":
				$this->logger->trace("Got name packet from " . $from->getIp() . ", " . $from->getName());
				$packet = new NamePacket($this);
				break;
			case "color":
				$this->logger->trace("Got color packet from " . $from->getIp() . ", " . $from->getName());
				$packet = new ColorPacket($this);
				break;
			default:
				break;
		}
		
		return $packet;
	}

	/**
	 *
	 * @param Client $from        	
	 * @param unknown $in        	
	 * @return Packet
	 */
	public function in(Client $from, $in)
	{
		$data = $this->unmask($in);
		$packet = null;
		$packetContent = json_decode($data["payload"], true);
		
		switch ($data["type"]) {
			case self::PACKET_TYPE_CLOSE:
				$this->logger->trace("Got disconnect (close) packet from " . $from->getIp() . ", " . $from->getName());
				$packet = new DisconnectPacket($this, DisconnectPacket::DISCONNECT_QUIT);
				break;
			
			case self::PACKET_TYPE_MESSAGE:
			default:
				$packet = $this->handleMessagePacket($from, $data, $packetContent);
				break;
		}
		
		if (! $packet) {
			$this->logger->warning("Unknown packet type '" . $data['type'] . "' from " . $from->getIp());
			return false;
		}
		
		$packet->setClient($from);
		
		$packet->fromArray($packetContent);
		
		return $packet;
	}

	public function out(Client $to, Packet $packet)
	{
		$this->logger->trace("Sending packet " . $packet->toJSON() . " to " . $to->getName());
		$to->send($this->mask($packet->toJSON()));
	}

	public function outToAll(Packet $packet)
	{
		$raw = $this->mask($packet->toJSON());
		foreach ($this->server->getClients() as $client) {
			$client->send($raw);
		}
	}

	public function mask($text)
	{
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);
		
		if ($length <= 125) {
			$header = pack("CC", $b1, $length);
		} elseif ($length > 125 && $length < 65536) {
			$header = pack("CCn", $b1, 126, $length);
		} elseif ($length >= 65536) {
			$header = pack("CCNN", $b1, 127, $length);
		}
		
		return $header . $text;
	}

	/**
	 * https://github.com/nekudo/php-websocket/blob/master/server/lib/WebSocket/Connection.php
	 *
	 * @param unknown $data        	
	 */
	public function unmask($data)
	{
		$payloadLength = '';
		$mask = '';
		$unmaskedPayload = '';
		$decodedData = array();
		
		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($data[0]));
		$secondByteBinary = sprintf('%08b', ord($data[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($data[1]) & 127;
		
		// close connection if unmasked frame is received:
		if ($isMasked === false) {
			$this->close(1002);
		}
		
		switch ($opcode) {
			// text frame:
			case 1:
				$decodedData['type'] = self::PACKET_TYPE_MESSAGE;
				break;
			
			case 2:
				$decodedData['type'] = self::PACKET_TYPE_BINARY;
				break;
			
			// connection close frame:
			case 8:
				$decodedData['type'] = self::PACKET_TYPE_CLOSE;
				break;
			
			// ping frame:
			case 9:
				$decodedData['type'] = self::PACKET_TYPE_PING;
				break;
			
			// pong frame:
			case 10:
				$decodedData['type'] = self::PACKET_TYPE_PONG;
				break;
			
			default:
				// Close connection on unknown opcode:
				$this->close(1003);
				break;
		}
		
		if ($payloadLength === 126) {
			$mask = substr($data, 4, 4);
			$payloadOffset = 8;
			$dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
		} elseif ($payloadLength === 127) {
			$mask = substr($data, 10, 4);
			$payloadOffset = 14;
			$tmp = '';
			for ($i = 0; $i < 8; $i ++) {
				$tmp .= sprintf('%08b', ord($data[$i + 2]));
			}
			$dataLength = bindec($tmp) + $payloadOffset;
			unset($tmp);
		} else {
			$mask = substr($data, 2, 4);
			$payloadOffset = 6;
			$dataLength = $payloadLength + $payloadOffset;
		}
		
		/**
		 * We have to check for large frames here.
		 * socket_recv cuts at 1024 bytes
		 * so if websocket-frame is > 1024 bytes we have to wait until whole
		 * data is transferd.
		 */
		if (strlen($data) < $dataLength) {
			return false;
		}
		
		if ($isMasked === true) {
			for ($i = $payloadOffset; $i < $dataLength; $i ++) {
				$j = $i - $payloadOffset;
				if (isset($data[$i])) {
					$unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
				}
			}
			$decodedData['payload'] = $unmaskedPayload;
		} else {
			$payloadOffset = $payloadOffset - 4;
			$decodedData['payload'] = substr($data, $payloadOffset);
		}
		
		return $decodedData;
	}
}
?>