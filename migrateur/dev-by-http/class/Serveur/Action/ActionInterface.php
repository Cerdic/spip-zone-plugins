<?php

namespace SPIP\Migrateur\Serveur\Action;

use SPIP\Migrateur\LoggerInterface;

/**
 * Décrit une action client
 */
interface ActionInterface
{

	/**
	 * Prépare l'action à partir des données reçues
	 *
	 * @return mixed Données à envoyer au serveur.
	 */
	public function run($data = null);

	/**
	 * Sets a logger.
	 * 
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger);

	/**
	 * Log a message
	 * 
	 * @param string message
	 */
	public function log($message);
}
