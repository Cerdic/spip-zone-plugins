<?php

// definir le chemin des librairies
// que l'on installe
if (!defined('_DIR_LIB')) {
	define('_DIR_LIB', _DIR_RACINE . 'lib/');
}

// passer ou pas en mode debug
if (!defined('_SVP_DEBUG')) {
	define('_SVP_DEBUG', false);
}


// Mode d'utilisation de SVP runtime ou pas :
// - En mode runtime (true), on ne charge que les plugins compatibles avec la version courante
// - En mode non runtime (false) on charge tous les plugins : cas du site Plugins SPIP
// Runtime est le mode par defaut
define('_SVP_MODE_RUNTIME', true);

// Mise a jour automatique des depots (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_DEPOTS', true);
// - Periode d'actualisation en nombre d'heures (de 1 a 24)
define('_SVP_PERIODE_ACTUALISATION_DEPOTS', 6);

// Type parseur XML a appliquer pour recuperer les infos du plugin 
// - plugin, pour utiliser plugin.xml 
// - paquet, pour paquet.xml 
define('_SVP_DTD_PLUGIN', 'plugin'); 
define('_SVP_DTD_PAQUET', 'paquet'); 

// Regexp de recherche des balises principales de archives.xml
define('_SVP_REGEXP_BALISE_DEPOT', '#<depot[^>]*>(.*)</depot>#Uims');
define('_SVP_REGEXP_BALISE_ARCHIVES', '#<archives[^>]*>(.*)</archives>#Uims');
define('_SVP_REGEXP_BALISE_ARCHIVE', '#<archive[^>]*>(.*)</archive>#Uims');
define('_SVP_REGEXP_BALISE_ZIP', '#<zip[^>]*>(.*)</zip>#Uims');
define('_SVP_REGEXP_BALISE_TRADUCTIONS', '#<traductions[^>]*>(.*)</traductions>#Uims');
define('_SVP_REGEXP_BALISE_PLUGIN', '#<plugin[^>]*>(.*)</plugin>#Uims');
define('_SVP_REGEXP_BALISE_PAQUET', '#<paquet[^>]*>(.*)</paquet>#Uims');
define('_SVP_REGEXP_BALISE_MULTIS', '#<multis[^>]*>(.*)</multis>#Uims');

// Liste des balises techniques autorisees dans la balise <spip> et des balises autorisant une traduction
$GLOBALS['balises_techniques'] = array(
	'menu', 'chemin', 'lib', 'necessite', 'onglet', 'procure', 'pipeline', 'utilise',
	'options', 'fonctions', 'install');
$GLOBALS['balises_multis'] = array(
	'nom', 'slogan', 'description');

// Liste des categories de plugin
$GLOBALS['categories_plugin'] = array(
	'auteur', 
	'communication', 
	'date', 
	'divers', 
	'edition', 
	'maintenance', 
	'multimedia', 
	'navigation', 
	'outil', 
	'performance', 
	'statistique', 
	'squelette', 
	'theme', 
	'aucune'
);

// Liste des licences de plugin
$GLOBALS['licences_plugin'] = array(
	'apache' => array(
		'versions' => array('2.0', '1.1', '1.0'),
		'nom' => 'Apache licence, version @version@',
		'url' => 'http://www.apache.org/licenses/LICENSE-@version@'),
	'art' => array(
		'versions' => array('1.3'),
		'nom' => 'Art libre @version@',
		'url' => 'http://artlibre.org/licence/lal'),
	'mit' => array(
		'versions' => array(),
		'nom' => 'MIT',
		'url' => 'http://opensource.org/licenses/mit-license.php'),
	'bsd' => array(
		'versions' => array(),
		'nom' => 'BSD',
		'url' => 'http://www.freebsd.org/copyright/license.html'),
	'agpl' => array(
		'versions' => array('3'),
		'nom' => 'AGPL @version@',
		'url' => 'http://www.gnu.org/licenses/agpl.html'),
	'fdl' => array(
		'versions' => array('1.3', '1.2', '1.1'),
		'nom' => 'FDL @version@',
		'url' => 'http://www.gnu.org/licenses/fdl-@version@.html'),
	'lgpl' => array(
		'versions' => array('3.0', '2.1'),
		'nom' => array('3.0' => 'LGPL 3', '2.1' => 'LGPL 2.1'),
		'url' => 'http://www.gnu.org/licenses/lgpl-@version@.html'),
	'gpl' => array(
		'versions' => array('3', '2', '1'),
		'nom' => 'GPL @version@',
		'url' => 'http://www.gnu.org/licenses/gpl-@version@.0.html'),
	'ccby' => array(
		'versions' => array('2.0', '2.5', '3.0'),
		'suffixes' => array('-sa', '-nc', '-nd', '-nc-nd', '-nc-sa'),
		'nom' => 'CC BY@suffixe@ @version@',
		'url' => 'http://creativecommons.org/licenses/by@suffixe@/@version@/')
);

// Version SPIP minimale quand un plugin ne le precise pas
// -- Version SPIP correspondant a l'apparition des plugins
define('_SVP_VERSION_SPIP_MIN', '1.9.0');
// -- Pour l'instant on ne connait pas la borne sup exacte
define('_SVP_VERSION_SPIP_MAX', '3.0.99');

// Branche SPIP stable
define('_SVP_BRANCHE_STABLE', '2.1');

// Liste des branches significatives de SPIP et de leurs bornes (versions min et max)
// A mettre a jour en fonction des sorties
$GLOBALS['infos_branches_spip'] = array(
	'1.9' => array(_SVP_VERSION_SPIP_MIN,'1.9.2'),
	'2.0' => array('2.0.0','2.0.99'),
	'2.1' => array('2.1.0','2.1.99'),
	'3.0' => array('3.0.0',_SVP_VERSION_SPIP_MAX) 
);

// Liste des pages publiques d'objet supportees par le squelette (depot, plugin, paquet).
// Par defaut, SVP n'en propose plus.
// Le squelette qui les propose doit definir la constante en suivant l'exemple ci-dessous :
// define('_SVP_PAGES_OBJET_PUBLIQUES', 'depot:plugin');

// urls propres en minuscules
define ('_url_minuscules',1);

?>
