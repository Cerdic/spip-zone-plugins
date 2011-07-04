<?php
/**
 * Gestionnaire de bannieres publicitaires
 *
 * Le statut des pub peut être :
 * - 0cree	=> cas exceptionnel de création via ADDS
 * - 1inactif
 * - 2actif
 * - 3obsolete
 * - 4rompu
 * - 5poubelle
 *
 * <b>Tailles classiques des bannières publicitaires</b>
 * - banniere : 468x60 px | 35 Ko
 * - skyscraper : 120x600 px | 50 Ko
 * - pave : 300x250 px | 50 Ko
 * - carre : 250x250 px | 50 Ko
 * - bouton (logos ...) : jusqu'à 120 px (120x60 px)
 * - pour les animations, recommander des gif animés de 15 secondes max
 *
 * <b>Les tarifs</b>
 * - CPM : coût pour mille affichages
 * - CPC : coût par clic
 *
 * @name 		Options
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.1 (09/2010)
 * @package		Pub Banner
 * @todo Faire une page 'ADDS' autonome, qui ne depende ni de SPIP (entete et pied) ni d'un article
 * @todo Il reste un probleme de lien (au clic) sur les bannieres flash ...
 * @todo inclure les prix par defaut pour les nouvelles bannieres
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors','1'); error_reporting(E_ALL);

// -----------------------
// Definitions
// -----------------------

/**
 * Pour forcer l'effacement des tables lors de la desinstall, meme s'ils sont pleins 
 * => desinstall impossible si des champs sont !=0
 * => define('PUBBAN_FORCE_UNINSTALL',1); pour forcer l'effacement des tables
 * => utilitaire de dev ou test
 */
define('PUBBAN_FORCE_UNINSTALL', 0);

/**
 * Adresse du displayer de pub
 */
define('_PUBBAN_ADDS_DISPLAYER', 'pub_displayer');

/**
 * Adresse du cliqueur
 */
define('_PUBBAN_ADDS_CLICKER', 'clic');

/**
 * Chemin vers les images PUB
 */
define('_DIR_IMGPUB', str_replace('../', $GLOBALS['meta']['adresse_site'].'/', _DIR_PLUGIN_PUBBAN).'img_pub' );

/**
 * Definition des puces selon le statut des pubs
 */
$GLOBALS['_PUBBAN_PUCES_STATUTS'] = array(
	'img' => array(
		'name' => 'Image',
		'value' => 'img',
		'icon' => _DIR_PLUGIN_PUBBAN."img/image.png",
	),
	'swf' => array(
		'name' => 'SWF object',
		'value' => 'swf',
		'icon' => _DIR_PLUGIN_PUBBAN."img/application_flash.gif",
	),
	'flash' => array(
		'name' => 'Flash object',
		'value' => 'flash',
		'icon' => _DIR_PLUGIN_PUBBAN."img/application_flash.gif",
	),
	'emplacement' => array(
		'name' => 'Emplacement',
		'icon' => _DIR_PLUGIN_PUBBAN."img/insert-image-16.png",
	),
);

/**
 * Definition des icones utilisees
 */
$GLOBALS['pubban_pub_icons'] = array(
	'default' => _DIR_PLUGIN_PUBBAN."img/gnome-text-x-readme.png",
	'bmp' => _DIR_PLUGIN_PUBBAN."img/gnome-image-bmp.png",
	'gif' => _DIR_PLUGIN_PUBBAN."img/gnome-image-gif.png",
	'jpeg' => _DIR_PLUGIN_PUBBAN."img/gnome-image-jpeg.png",
	'jpg' => _DIR_PLUGIN_PUBBAN."img/gnome-image-jpeg.png",
	'png' => _DIR_PLUGIN_PUBBAN."img/gnome-image-png.png",
	'swf' => _DIR_PLUGIN_PUBBAN."img/gnome-flash.png",
	'flash' => _DIR_PLUGIN_PUBBAN."img/gnome-flash.png",
);

/**
 * Definition des boutons
 */
$GLOBALS['pubban_btns'] = array(
	'apercu' => _DIR_PLUGIN_PUBBAN."img/stock_search-16.png",
	'editer' => _DIR_PLUGIN_PUBBAN."img/stock_edit-16.png",
	'poubelle' => _DIR_PLUGIN_PUBBAN."img/stock_delete-16.png",
	'sortie_poubelle' => _DIR_PLUGIN_PUBBAN."img/stock_undelete-16.png",
	'inactif' => _DIR_PLUGIN_PUBBAN."img/cross.png",
	'actif' => _DIR_PLUGIN_PUBBAN."img/thumb_up.png",
	'obsolete' => _DIR_PLUGIN_PUBBAN."img/clock_stop.png",
	'lister' => _DIR_PLUGIN_PUBBAN."img/stock_open-16.png",
);

/**
 * URL de documentation/information
 */
define('_PUBBAN_URL', 'http://www.spip-contrib.net/?article3637');
/**
 * URL de telechargement des mises a jour
 */
define('_PUBBAN_UPDATE', 'http://files.spip.org/spip-zone/pub_banner.zip');
/**
 * Traceur de dev.
 */
define('_PUBBAN_TRAC', 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/pub_banner');

// charger la config
include_spip('inc/pubban_configset');

// Si admin, lib pubban_prive
if(test_espace_prive())
	include_spip('inc/pubban_prive');

// ----------------------------
// FONCTIONS RECUPERATION DES DONNEES
// ----------------------------

/**
 * Recuperation des donnes d'une publicite
 * @param	integer	$id_pub	L'ID de la pub a recuperer
 * @param	string	$str	Le nom d'un paramètre à récupérer (optionnel)
 * @return array	Les données de la pub (ou la valeur du paramètre si demandé)
 */
function pubban_recuperer_pub($id_pub, $str=false) {
	include_spip('base/abstract_sql');
	$vals = array();
	if($id_pub != '0') {
		$resultat = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_pub'],"id_pub=".intval($id_pub)	, '', '', '', '', _BDD_PUBBAN);
		if (sql_count($resultat) > 0) {
			while ($row=spip_fetch_array($resultat)) {
				$vals['id'] = $id_pub;
				$vals['type'] = $row['type'];
				$vals['titre'] = $row['titre'];
				$vals['url'] = $row['url'];
				$vals['objet'] = $row['objet'];
				$vals['illimite'] = $row['illimite'];
				$vals['affichages'] = $row['affichages'];
				$vals['clics'] = $row['clics'];
				$vals['affichages_restant'] = $row['affichages_restant'];
				$vals['clics_restant'] = $row['clics_restant'];
				$vals['date_debut'] = $row['date_debut'];
				$vals['date_fin'] = $row['date_fin'];
				$vals['date_add'] = $row['date_add'];
				$vals['statut'] = $row['statut'];
			}
			sql_free($resultat, _BDD_PUBBAN);
		}
		$resultat_empl = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_join'],"id_pub=".intval($id_pub), '', '', '', '', _BDD_PUBBAN);
		if (sql_count($resultat_empl) > 0) {
			while ($row_empl=spip_fetch_array($resultat_empl)) {
				$vals['emplacement'][] = $row_empl['id_empl'];
			}
			sql_free($resultat_empl, _BDD_PUBBAN);
		}
	}
	if($str){
		if( isset($vals[$str]) ) return $vals[$str];
		return false;
	}
	return $vals;
}

function pubban_comparer_emplacements($emp){
	if(!is_array($emp)) return;
	if(count($emp) > 1) {
		$width = $height = array();
		foreach($emp as $k=>$empl){
			$width[] = pubban_recuperer_emplacement($empl, 'width');
			$height[] = pubban_recuperer_emplacement($empl, 'height');
		}
		if( count(array_unique($width)) != 1 OR 
			count(array_unique($height)) != 1
		) return false;
	}
	return true;
}

/**
 * Recuperation des donnes d'une banniere
 * @param	integer	$id_empl	L'ID de la bannière à récuperer
 * @param	string	$str	Le nom d'un paramètre à récupérer (optionnel)
 * @return array	Les données de la banniere (ou la valeur du paramètre si demandé)
 */
function pubban_recuperer_emplacement($id_empl, $str=false) {
	include_spip('base/abstract_sql');
	$vals = array();
	if($id_empl != '0') {
		$resultat = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_empl'],"id_empl=".intval($id_empl), '', '', '', '', _BDD_PUBBAN);
		if (sql_count($resultat) > 0) {
			while ($row=spip_fetch_array($resultat)) {
				$vals['id'] = $id_empl;
				$vals['titre'] = $row['titre'];
				$vals['titre_id'] = $row['titre_id'];
				$vals['width'] = $row['width'];
				$vals['height'] = $row['height'];
				$vals['ratio_pages'] = $row['ratio_pages'];
				$vals['statut'] = $row['statut'];
/*
				$vals['prix_tranche_1'] = $row['prix_tranche1'];
				$vals['prix_tranche_2'] = $row['prix_tranche2'];
				$vals['prix_tranche_3'] = $row['prix_tranche3'];
				$vals['prix_tranche_4'] = $row['prix_tranche4'];
*/
			}
			sql_free($resultat, _BDD_PUBBAN);
		}
	}
	if($str){
		if( isset($vals[$str]) ) return $vals[$str];
		return false;
	}
	return $vals;
}

/**
 * Recuperation de l'ID d'une banniere depuis son nom
 * @param	string	$name	Le nom de la banniere a recuperer
 * @return integer	L'ID recherche
 */
function pubban_recuperer_emplacement_par_nom($name) {
	include_spip('base/abstract_sql');

	// Si c'est un "id" on renvoie
	if (is_numeric($name))
		return pubban_recuperer_emplacement($name);

	// Par "titre_id"
	$id_empl = sql_getfetsel("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], "titre_id=".sql_quote($name), '', '', '', '', _BDD_PUBBAN);
	if($id_empl)
		return pubban_recuperer_emplacement($id_empl);

	// Par "titre" (compatibilite)
	$id_empl = sql_getfetsel("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], "titre LIKE ('$name')", '', '', '', '', _BDD_PUBBAN);
	if($id_empl)
		return pubban_recuperer_emplacement($id_empl);

	// Sinon nada
	return false;
}

function pubban_liste_empl($statut=false){
	include_spip('base/abstract_sql');
	$emplacements = array();
	if($statut AND !is_array($statut))
		$statut = array( $statut );
	$where = $statut ? "statut IN ('".join("','", $statut)."')" : '';
	$resultat = sql_select("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], $where, '', '', '', '', _BDD_PUBBAN);
	if (sql_count($resultat) > 0) {
		while ($row=spip_fetch_array($resultat)) {
			$emplacements[] = $row['id_empl'];
		}
	}
	return $emplacements;
}

function pubban_trouver_emplacements($id_pub){
	if($id_pub == '0') return;
	include_spip('base/abstract_sql');
	$emplacements = array();
	$resultat = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_join'], 'id_pub='.intval($id_pub), '', '', '', '', _BDD_PUBBAN);
	if (sql_count($resultat) > 0) {
		while ($row=spip_fetch_array($resultat)) {
			$emplacements[] = $row['id_empl'];
		}
	}
	return $emplacements;
}

function pubban_transformer_nombre($nombre){
	$nombre = str_replace(' ', '', $nombre);
	$nombre = str_replace(',', '.', $nombre);
	return trim($nombre);
}

function pubban_transformer_titre_id($str){
	$str = str_replace(' ', '_', utf8_encode($str));
	return trim($str);
}

?>