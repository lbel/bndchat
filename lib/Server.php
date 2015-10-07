<?php
use Configuration\Configuration;

use Client\Client;

use Client\Dao\ClientStorage;
use Client\Dao\LoggingClientStorage;
use Client\Dao\LocalClientStorage;

use Client\PacketHandler;
use Client\Packet\Packet;
use Client\Packet\ConnectPacket;
use Client\Packet\DisconnectPacket;
use Client\Packet\UsersPacket;

use Log\Logger;
use Log\LogFactory;
use User\User;
use User\UserComponent;

class Server
{
	/**
	 *
	 * @var Configuration
	 */
	protected $config;
	/**
	 *
	 * @var ClientStorage
	 */
	protected $clients;
	/**
	 *
	 * @var PacketHandler
	 */
	protected $packetHandler;
	/**
	 *
	 * @var int
	 */
	protected $port;
	/**
	 *
	 * @var Logger
	 */
	protected $logger;
	
	private $userDao;

	/**
	 * TODO: Make this into a listener.
	 * These are components which ONLY rely on the existence of the 
	 * Client object. No User is known yet.
	 * 
	 * @var Component[]
	 */
	private $clientComponentList = array();
	
	public function __construct( Configuration $configuration )
	{
		$this->config = $configuration;
		$this->logger = LogFactory::get("Server");
		$this->clients = new LoggingClientStorage(new LocalClientStorage($this), LogFactory::get("LocalClientStorage"));
		$this->packetHandler = new PacketHandler($this);
		$this->port = (int) $this->config->getPort();
		
		$this->userDao = $configuration->getUserDao();
		//$this->clientComponentList[] = new UserComponent( $this->userDao );
	}

	/**
	 *
	 * @return number
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * @return ClientStorage
	 */
	public function getClients()
	{
		return $this->clients;
	}

	public function start()
	{
		$null = null;
		$buffer = null;
		$this->logger->info("Creating listening socket on port " . $this->port);
		$listenSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($listenSocket, 0, $this->port);
		socket_listen($listenSocket);
		$this->clients->setListenSocket($listenSocket);
		$listenSocketHash = Client::socketHash($listenSocket);
		while (true) {
			$changed = $this->clients->getSockets();
			socket_select($changed, $null, $null, 0, 10);
			if (isset($changed[$listenSocketHash]))
			{
				// New connection
				$newSocket = socket_accept($listenSocket);
				$client = new Client($this, $newSocket);
				$this->clients->addClient($client);
				
				foreach ($this->clientComponentList as $crucialComponent)
				{
					$crucialComponent->onConnect( $client );
				}

				$packet = new ConnectPacket($this->packetHandler);
				$packet->setClient($client);
				$packet->process();

				$packet = new UsersPacket($this->packetHandler);
				$packet->setClient($client);
				$packet->process();

				unset($changed[$listenSocketHash]);
			}
			
			foreach ($changed as $socket) {
				$skip = false;
				while (socket_recv($socket, $buffer, 131072, 0) >= 1) {
					$skip = true;
					$client = $this->clients->getBySocket($socket);
					$packet = $this->packetHandler->in($client, $buffer);
					if ($packet instanceof Packet) {
						$packet->process();
					}
					// $this->logger->info("Client " . $client->getIP() . " message: ".$unmask);
					break;
				}
				if ($skip) {
					continue;
				}
				$buffer = @socket_read($socket, 1024, PHP_NORMAL_READ);
				if ($buffer === false) { // check disconnected client
					$client = $this->clients->getBySocket($socket);

					if (!$client) {
						$this->logger->error("Unknown client disconnected - this should never happen");
						throw new Exception("Unknown client disconnected?");
					} else {
						foreach ($this->clientComponentList as $crucialComponent)
						{
							$crucialComponent->onClose( $client );
						}

						$packet = new DisconnectPacket($this->packetHandler, DisconnectPacket::DISCONNECT_TIMEOUT);
						$packet->setClient($client);
						$packet->process();

						$this->clients->removeClient($client);
						unset($client);
					}
				}
			}
		}
	}
}
?>