<?php

namespace SPIP\Prestashop;

include_spip('lib/PSWebServiceLibrary');
include_spip('inc/prestashop_ws_utils');

class Webservice extends \PrestaShopWebservice {

	protected static $_instance_by_lang = [];

	/**
	 * Returns new or existing Webservice instance
	 *
	 * Le webservice utilisera l'URL d'une boutique prestashop
	 * dans la langue demandée (si possible). Sinon on se rabat
	 * sur l'URL de configuration.
	 *
	 * @param string $lang Code de langue.
	 * @return Webservice
	 */
	final public static function getInstanceByLang($lang = null) {
		if (!$lang) {
			$lang = $GLOBALS['spip_lang'];
		}
		if (!empty(static::$_instance_by_lang[$lang])){
			return static::$_instance_by_lang[$lang];
		}
		$url = prestashop_ws_list_shops_by_lang($lang);
		static::$_instance_by_lang[$lang] = new static($url);
		return static::$_instance_by_lang[$lang];
	}

	/**
	 * Webservice constructor.
	 * On passe l'url et la clé configurée.
	 * @param string|null $url
	 *     URL du prestashop (utilise l'URL configurée par défaut si null)
	 * @param string|null $cle
	 *     Clé d'API (utilise la clé configurée par défaut si null)
	 * @param bool|null $debug
	 *     Mode débug. true/false pour activer désactiver. Null: automatique avec
	 *     var_mode=prestashop (+ autorisation debug).
	 */
	function __construct($url = null, $cle = null, $debug = null) {
		include_spip('inc/config');
		if (is_null($url)) {
			$url = lire_config('prestashop_api/url');
		}
		if (is_null($cle)) {
			$cle = lire_config('prestashop_api/cle');
		}
		if (is_null($debug)) {
			$debug = (_request('var_mode') === 'prestashop' AND include_spip('inc/autoriser') AND autoriser('debug'));
		}
		if ($url) {
			$url = trim(rtrim($url, '/'));
		}
		parent::__construct($url, $cle, $debug);
	}

	/**
	 * On autorise un SSL non vérifié
	 *
	 * @note CHIOTTE ! ils utilisent self::executeRequest()
	 *    dans leurs méthodes, ce qui fait qu'on ne peut
	 *    pas surcharger la méthode (la notre n'est pas appelée).
	 *    (vive static::).
	 *
	 * @param string $url
	 * @param array $curl_params
	 */
	public function executeRequest($url, $curl_params = array()) {
		$curl_params += [
			CURLOPT_SSL_VERIFYPEER => 0
		];
		return parent::executeRequest($url, $curl_params);
	}

	/**
	 * Retourne la description d'une resource (ses champs)
	 * @param string $resource
	 * @return simpleXML
	 */
	public function show($resource) {
		$url = $this->url . '/api/' . $resource;
		$url = parametre_url($url, 'schema', 'synopsis', '&');
		return $this->get(['url' => $url]);
	}
}

