<?php
namespace Client;

use User\User;

class Client implements \Hashable
{
	/**
	 *
	 * @var Server
	 */
	protected $server;

	protected $socket;

	protected $hash;

	protected $ip;

	/**
	 * @var User
	 */
	protected $user;

	protected $name;

	protected $color;

	private $handshaked = false;

	public $waitingForData = false;

	private $_dataBuffer = "";
	
	private $socketName;

	public function __construct(\Server $server, $socket)
	{
		$this->server = $server;
		$this->socket = $socket;
		$this->hash = self::socketHash($socket);
		$this->doHandShake();
		$this->refreshIP();

		$ipParts = explode(".", $this->ip);

		$this->name = $ipParts[(count($ipParts)-1)]."_".$this->randomStr();
		$this->color = $this->randomColor();

		// $this->connectionId = md5($this->ip . $this->port . spl_object_hash($this));
	}

	public function randomStr($len = 5) {
		$parts = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
		);
		$countParts = count($parts)-1;
		$out = "";
		for ($i=0;$i<$len;$i++) {
			$out .= $parts[mt_rand(0, $countParts)];
		}
		return $out;
	}

	public function randomColor() {
		$parts = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");
		$countParts = count($parts)-1;
		$out = "";
		for ($i=0;$i<6;$i++) {
			$out .= $parts[mt_rand(0, $countParts)];
		}
		return "#".$out;
	}

	protected function doHandShake()
	{
		$header = socket_read($this->socket, 1024); // read data sent by the socket
		
		$headers = array();
		$lines = preg_split("/\r\n/", $header);
		foreach ($lines as $line) {
			$line = chop($line);
			if (preg_match("/\A(\S+): (.*)\z/", $line, $matches)) {
				$headers[$matches[1]] = $matches[2];
			}
		}
		
		$secKey = $headers['Sec-WebSocket-Key'];
		$secAccept = base64_encode(pack("H*", sha1($secKey . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11")));
		$upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" . "Upgrade: websocket\r\n" . "Connection: Upgrade\r\n" . 
		// "WebSocket-Origin: $host\r\n" .
		// "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
		"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		socket_write($this->socket, $upgrade, strlen($upgrade));
		
		$this->handshaked = true;
	}

	protected function refreshIP()
	{
		$ip = "";
		socket_getpeername($this->socket, $ip);
		$this->ip = $ip;
	}

	public function send($raw) {
		@socket_write($this->socket, $raw, strlen($raw));
	}

	public function getSocket()
	{
		return $this->socket;
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function getIP()
	{
		return $this->ip;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getColor() {
		return $this->color;
	}

	public function setColor($color) {
		$this->color = $color;
	}

	public function destroy()
	{
		$this->server = null;
		$this->socket = null;
		$this->user = null;
	}
	
	public function getUser()
	{
		return $this->user;	
	}

	public static function socketHash( $socket )
	{
		return (int) $socket;
	}
	
	/**
	 * NOTE: If you set the default of an object value to null, null is 
	 * accepted as an argument. Else it is not... PHP...
	 * 
	 * @param User $user
	 */
	public function setUser( User $user=null )
	{
		$this->user = $user;	
	}
}
?>