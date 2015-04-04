<?php

namespace SPIP\Migrateur\Serveur;

class Log extends \SPIP\Migrateur\Client\Log {

	/**
	 * Logs with an arbitrary level.
	 *
	 * Ajoute un message de log dans tmp/migrateur/serveur.log
	 * et tmp/log/migrateur_serveur.log !
	 * 
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, array $context = array()) {
		static $done   = false;

		$dir = _DIR_TMP . 'migrateur';
		if (!$done) {
			sous_repertoire(_DIR_TMP . 'migrateur');
			$done = true;
		}

		file_put_contents($dir . "/serveur.log", date("Y:m:d H:i:s") . " | " . substr($level, 0, 4) . " | " . $message . "\n", FILE_APPEND);
		spip_log($message, 'migrateur_serveur');
	}

}
