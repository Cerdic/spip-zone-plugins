<?php

namespace SPIP\Migrateur\Client\Action;

use SPIP\Migrateur\LoggerInterface;
use SPIP\Migrateur\Client;
use SPIP\Migrateur\Data;

/**
 * Décrit une action client
 */
class ActionBase implements ActionInterface
{
	/** @var LoggerInterface */
	protected $logger;

	/** @var Parent */
	protected $client;

	/** @var Data */
	protected $destination;

	/**
	 * Prépare l'action à partir des données reçues
	 *
	 * @return mixed Données à envoyer au serveur.
	 */
	public function run($data = null) {
		return null;
	}

	/**
	 * Sets a parent class.
	 * 
	 * @param Client $client
	 */
	public function setParent(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Sets a destination class.
	 * 
	 * @param Data $destination
	 */
	public function setDestination(Data $destination)
	{
		$this->destination = $destination;
	}


	/**
	 * Sets a logger.
	 * 
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Log a message
	 * 
	 * @param string message
	 */
	public function log($message)
	{
		if ($this->logger) {
			$this->logger->info($message);
		}
	}

	/**
	 * Log a start run message
	 * 
	 * @param string message
	 */
	public function log_run($message)
	{
		$this->log("<strong>$message</strong>");
	}
}
