<?php

namespace SPIP\Migrateur\Serveur\Action;

use SPIP\Migrateur\LoggerInterface;
use SPIP\Migrateur\Data;
use SPIP\Migrateur\Serveur;

/**
 * Décrit une action client
 */
class ActionBase implements ActionInterface
{
	/** @var SPIP\Migrateur\LoggerInterface */
	protected $logger;

	/** @var SPIP\Migrateur\Data */
	protected $source;

	/** @var SPIP\Migrateur\Serveur */
	protected $serveur;


	/**
	 * Prépare l'action à partir des données reçues
	 *
	 * @return mixed Données à envoyer au serveur.
	 */
	public function run($data = null) {
		return null;
	}

	/**
	 * Sets a source description.
	 * 
	 * @param Data $source
	 */
	public function setSource(Data $source)
	{
		$this->source = $source;
	}

	/**
	 * Sets a serveur.
	 * 
	 * @param Serveur $serveur
	 */
	public function setServeur(Serveur $serveur)
	{
		$this->serveur = $serveur;
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
