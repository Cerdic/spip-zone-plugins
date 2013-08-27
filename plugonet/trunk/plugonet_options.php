<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Le serveur d'aide en ligne pour ce plugin
$GLOBALS['help_server'][] ='http://plugins.spip.net/aide/';
$GLOBALS['index_aide_plugonet'] = array(
	'paqxmlgen',
	'paqxmlpaquet',
	'paqxmlnom',
	'paqxmldesc',
	'paqxmlaut',
	'paqxmlcred',
	'paqxmlcopy',
	'paqxmllic',
	'paqxmltrad',
	'paqxmlbout',
	'paqxmlpipe',
	'paqxmlnec',
	'paqxmllib',
	'paqxmlproc',
	'paqxmlpath',
	'paqxmlspip',
	'paqxmlfoi',
	'paqxmlexe'
);

// Version SPIP minimale quand un plugin ne le precise pas
// -- Version SPIP correspondant a l'apparition des plugins
define('_PLUGONET_VERSION_SPIP_MIN', '1.9.0');
// -- Pour l'instant on ne connait pas la borne sup exacte
define('_PLUGONET_VERSION_SPIP_MAX', '3.0.99');

// Balises dites techniques contenues dans le fichier plugin.xml
$GLOBALS['balises_techniques_plugin'] = array(
	'menu', 'chemin', 'lib', 'necessite', 'onglet', 'procure', 'pipeline', 'utilise',
	'options', 'fonctions', 'install');

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
?>
