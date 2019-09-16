<?php
/**
 * Fonctions utiles au plugin Links
 *
 * @plugin     Links
 * @copyright  2009-2019
 * @author     Collectif
 * @licence    GNU/GPL
 * @package    SPIP\Links\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Gerer les articles virtuels rediriges
 * on ne traite que les #URL_ARTICLE, pas les #URL_ARTICLE{13}
 * @param $p
 * @return mixed
 */
function balise_URL_ARTICLE($p){
	include_spip('balise/url_');
	balise_URL_ARTICLE_dist($p); // traitement de base de SPIP

	if ($p->type_requete=='articles' AND !interprete_argument_balise(1, $p)){
		include_spip('inc/lien');
		$_virtuel = champ_sql('virtuel', $p);
		$_redirige = (function_exists('virtuel_redirige') ? "virtuel_redirige($_virtuel,true)" : "($_virtuel)");
		$p->code = "(($_virtuel)?$_redirige:" . $p->code . ')';
	}
	return $p;
}

/**
 * Lire la configuration
 * @return array|mixed
 */
function links_configuration(){
	static $config;
	if (is_null($config)) {
		$defaut = [
			'style' => 'off',
			'external' => 'off',
			'download' => 'off',
			'window' => 'off',
			'doc_list' => ".pdf,.ppt,.xls,.doc",
		];
		$config = [];
		if (isset($GLOBALS['meta']['links'])) {
			$config = unserialize($GLOBALS['meta']['links']);
			if (!$config) {
				$config = [];
			}
		}
		$config = array_merge($defaut, $config);
	}

	return $config;
}

/**
 * Inserer les CSS
 * @param $flux
 * @return string
 */
function links_insert_head_css($flux){
	//Recuperation de la configuration
	$links = links_configuration();

	//Styles
	if ($links['style']=='on'){
		$flux .= '<link rel="stylesheet" href="' . find_in_path('css/links.css') . '" type="text/css" media="all" />';
	}
	//Ouverture d'une nouvelle fenetre : insertion des init js inline, en amont des CSS (perf issue)
	if ($links['window']=='on'){
		$js = 'var js_nouvelle_fenetre=\'' . _T('links:js_nouvelle_fenetre') . '\';';
		//Ouverture dune nouvelel fenetre sur les liens externes
		if ($links['external']=='on'){
			// quand un site fait du multidomaine on prend en reference le domaine de la page concernee :
			// sur www.example.org : autre.example.org est external
			// sur autre.example.org : www.example.org est external
			// sur un site mono-domaine ca ne change rien :)
			// ca marche parce que le cache change quand le HTTP_HOST change (donc quand le domaine change)
			$js .= 'var links_site = \'' . protocole_implicite(url_de_base()) . '\';';
		}
		//Ouverture d'une nouvelle fenetre sur les documents (extensions a preciser)
		if (($links['download']=='on') && ($links['doc_list'])){
			$js .= 'var links_doc = \'' . $links['doc_list'] . '\';';
		}
		$flux = '<script type="text/javascript">' . $js . '</script>' . "\n" . $flux;
	}

	return $flux;
}

/**
 * Inserer le JS
 * @param $flux
 * @return string
 */
function links_insert_head($flux){
	//Recuperation de la configuration
	$links = links_configuration();

	//Ouverture d'une nouvelle fenetre
	if ($links['window']=='on'){
		$flux .= '<script src="' . find_in_path('links.js') . '" type="text/javascript"></script>' . "\n";
	}
	return $flux;
}
