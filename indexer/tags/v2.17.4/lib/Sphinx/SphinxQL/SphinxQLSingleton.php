<?php

namespace Sphinx\SphinxQL;

/**
 * Singleton pour récupérer un unique SphinxQL pour un host/port donné
 */
class SphinxQLSingleton {
	/**
	 * @var Singleton
	 * @access private
	 * @static
	 */
	private static $_instances = [];

	/**
	 * Méthode qui crée l'unique instance de la classe
	 * si elle n'existe pas encore puis la retourne.
	 *
	 * @param string $host
	 * @param int $port
	 * @return SphinxQL
	 */
	public static function getInstance($host = '127.0.0.1', $port = 9306) {
		$key = md5($host . $port);
		if (empty(self::$_instances[$key])) {
			self::$_instances[$key] = new SphinxQL($host, $port);
		}
		return self::$_instances[$key];
	}

	private function __construct(){ }
	private function __clone(){ }
}