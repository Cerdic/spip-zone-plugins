<?php

/*
 * Copyright (C) 2006 Cedric Morin
 * Licence GPL
 *
 * Plugin SPIP 1.9 (c) 2006 par Notre-ville.net
 * Web : http://www.notre-ville.net
 * Cedric MORIN (cedric.morin@notre-ville.net)
 *
 */

if (!defined('_DIR_PLUGIN_COMARQUAGE')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_COMARQUAGE',(_DIR_PLUGINS.end($p)).'/');
}

define('_DIR_PLUGIN_COMARQUAGE_IMAGES',_DIR_PLUGIN_COMARQUAGE."images");
// atention, ces 2 constantes ne sont pas utilisees partout
// on utilise sous_repertoire(_DIR_CACHE,'cache') et sous_repertoire(_DIR_CACHE,'xml')
// pour assurer la creation des repertoires
define('_DIR_CACHE_COMARQUAGE_XML',"comarq_xml"); // sous repertoire de _DIR_CACHE
define('_DIR_CACHE_COMARQUAGE_CACHE',"comarq_cache");// sous repertoire de _DIR_CACHE

// initialisation des meta par defaut
if (!isset($GLOBALS['meta']['comarquage_xml_server'])){
	include_spip('inc/meta');
	ecrire_meta('comarquage_xml_server','http://lecomarquage.service-public.fr/xml2');
	ecrire_metas();
}
if (!isset($GLOBALS['meta']['comarquage_local_refresh'])){
	include_spip('inc/meta');
	ecrire_meta('comarquage_local_refresh','259200'); /* 60*60*24*3 */
	ecrire_metas();
}
if (!isset($GLOBALS['meta']['comarquage_local_timeout'])){
	include_spip('inc/meta');
	ecrire_meta('comarquage_local_timeout','604800'); /* 60*60*24*7 */
	ecrire_metas();
}

if (!isset($GLOBALS['meta']['comarquage_default_xml_file'])){
	include_spip('inc/meta');
	ecrire_meta('comarquage_default_xml_file','Themes.xml');
	ecrire_metas();
}
if (!isset($GLOBALS['meta']['comarquage_default_xsl_file'])){
	include_spip('inc/meta');
	ecrire_meta('comarquage_default_xsl_file','Themes.xsl');
	ecrire_metas();
}


// recuperer le contenu compile d'une page xml
function & comarquage_compile_page_xml($parametres,$url_base){
	// regarder si la page parsee est en cache et valide
	comarquage_prepare_parametres_cache($parametres,$url_base);
	if ($ma_page =& comarquage_lire_cache($parametres))
	  return $ma_page;

	// sinon la parser

	// s'assurer que la feuille de style est bien la
	if (!file_exists($parametres['xsl_full_path'])) {
		comarquage_error("la feuille de style XSL '$parametres[xsl]' n'existe pas");
		return -10;
	}
	
	// rapatrier tous les fichiers xml necessaires au parsing
	// fichier principal + dependances
	if (!comarquage_prepare_fichiers_xml($parametres))
		return -20;
	
	// definir les parametres xsl
	$parametres_xsl = array();
	$parametres_xsl['IMAGESURL'] = _DIR_PLUGIN_COMARQUAGE_IMAGES;
	
	if (isset($parametres['lettre']))	$parametres_xsl['LETTRE'] = $parametres['lettre'];
	if (isset($parametres['motcle'])) $parametres_xsl['MOTCLE'] = $parametres['motcle'];
	$parametres_xsl['REFERER'] = $url_base;
	
	$ma_page =& comarquage_transforme_fichier_xml($parametres['xml_full_path'],$parametres['xsl_full_path'], $parametres_xsl);
	if ($ma_page === FALSE) {
		comarquage_error("le processeur XSLT a retourné une erreur fatale; l'action ne peut pas continuer");
		return -40;
	}
	
	$ma_page = implode("\n", $ma_page)."\n";
	// ecrire le fichier cache pour le prochain coup
	ecrire_fichier ($parametres['cache_full_path'], $ma_page);
	
	return $ma_page;
}

// rapatrier tout le contenu necessaire pour effectuer le rendu
function comarquage_prepare_fichiers_xml($parametres, $profondeur = 2){
	static $parsed=array();
	if (isset($parsed[$parametres['xml_full_path']]))
		return $parsed[$parametres['xml_full_path']];

	$ma_page ="";
	$mise_a_jour = comarquage_lire_xml($parametres, $ma_page);
	if ($mise_a_jour == FALSE){
		$parsed[$parametres['xml_full_path']] = FALSE;
		return FALSE;
	}
	$parsed[$parametres['xml_full_path']] = TRUE;
	
	if ($profondeur>0 && $mise_a_jour !==FALSE && $parametres['xml']{0} != 'M') {
		$liste_ressources = comarquage_extraire_ressources($parametres['xml_full_path'], $ma_page);
    if ($liste_ressources !== FALSE) 
			foreach ($liste_ressources as $v){
				$pars = array_merge($parametres, array('xml' => $v,'xml_full_path' => dirname($parametres['xml_full_path']).'/'.$v));
				comarquage_prepare_fichiers_xml($pars, $profondeur-1);
			}

		if ($liste_ressources === FALSE) {
			comarquage_error("impossible de recuperer les ressources associees au fichier $parametres[xml_full_path]");
			return FALSE;
		}
  }

	return TRUE;
}

 
// recuperer toutes les ressouces associees a un fichier xml
// dans un tableau
function comarquage_extraire_ressources($fichier_xml, $ma_page){
	$liste_ressources=array();
	include_spip('inc/plugin');
	include_spip('inc/filtres');
	$arbre = parse_plugin_xml($ma_page);
	if (is_array($arbre)){
		$arbre = reset($arbre); // prendre le noeud racine
		$arbre = $arbre[0];
		if (isset($arbre['RessourcesRattachées']))
			foreach($arbre['RessourcesRattachées'] as $subtree)
				foreach($subtree as $tag=>$val){
					$f = extraire_attribut("<$tag>",'lien');
					$f = basename($f,'.xml').'.xml';
					$liste_ressources[]=$f;
				}
	}
	return $liste_ressources;
}
 
function & comarquage_transforme_fichier_xml($fichier_xml, $fichier_xsl = NULL, $parametres = NULL){
	static $_executable = 'xsltproc';
	
	$params = " --path '"._DIR_CACHE._DIR_CACHE_COMARQUAGE_XML."/' ";
	if (is_array($parametres))
		foreach ($parametres as $k => $v)
			$params .= '--stringparam '.escapeshellarg($k).' '.escapeshellarg($v).' ';
	
	$fichier_erreur = tempnam('/tmp', 'xsltprocErrors_');
	$commande = $_executable . $params . ($fichier_xsl ? $fichier_xsl.' ' : '');
	$commande .= "$fichier_xml 2> $fichier_erreur";
	
	exec($commande, $retour, $erreur_code);
	
	if (filesize($fichier_erreur)) {
		lire_fichier($fichier_erreur,$message);
		comarquage_error("la commande '$_executable $params' a retourné ($erreur_code) : $message");
	}
	
	unlink($fichier_erreur);
	return $erreur_code ? FALSE : $retour;
}
  
// definir le nom du fichier de stockage de la page en cache
function comarquage_prepare_parametres_cache(& $parametres,$url_base){
	$cache_id = '';
	if (isset($parametres['lettre'])) $cache_id .= 'l'.$parametres['lettre'];
	if (isset($parametres['motcle'])) $cache_id .= 'm'.md5($parametres['motcle']);
	$cache_id .= md5($url_base);
  
	$parametres['cache_full_path'] = sous_repertoire(_DIR_CACHE,_DIR_CACHE_COMARQUAGE_CACHE).
		basename($parametres['xml'], '.xml').
		($cache_id ? '.'.$cache_id : '').'.cache';
}

// lire le fichier xml parse en cache
function & comarquage_lire_cache($parametres) {
	$fichier = $parametres['cache_full_path'];
	if (file_exists($fichier) &&
		filemtime($fichier) > filemtime($parametres['xml_full_path']) &&
		filemtime($fichier) > filemtime(dirname($parametres['xsl_full_path']))) {
		
		$ma_page = "";
		if (lire_fichier ($fichier, $ma_page))
			return $ma_page;
	}
	
	return FALSE;
}

function & comarquage_lire_xml($parametres, &$ma_page) {
	$fichier = $parametres['xml_full_path'];
	// on ne recharge pas la page ici du moment qu'elle n'est pas trop vieille
	// la reactualisation des pages est réalisée preferentiellement par tache cron
	if (($ok = file_exists($parametres['xml_full_path'])) &&
		time() - filemtime($parametres['xml_full_path']) < $GLOBALS['meta']['comarquage_local_timeout']) {
		$mise_a_jour = 10;
	}
	else if (!$mise_a_jour = comarquage_recuperer_page_xml($parametres)) {
		comarquage_error("erreur de telechargement du fichier $parametres[xml]; ".
		($ok ? "l'action continue avec le fichier présent dans le cache ".
		"mais la connexion au serveur externe doit être retablie" :
		"l'action ne peut pas être poursuivie car le fichier n'existe pas ".
		"dans le cache"));
		if ($ok==FALSE) return FALSE;
		$mise_a_jour = 10;
	}
	if (lire_fichier ($fichier, $ma_page))
		return $mise_a_jour;
	else
		return FALSE;
}

// recuperer un fichier xml sur un serveur distant
// retourne FALSE en cas d'echec
// 20 en cas de telechargement correct
function comarquage_recuperer_page_xml($parametres){
	$url = $GLOBALS['meta']['comarquage_xml_server'];
	$url = "$url/".$parametres['xml'];
	
	include_spip('inc/distant');
	$ma_page = recuperer_page($url);
	
	if ($ma_page===FALSE || !strlen($ma_page)) return FALSE;
	
	/* Return 20 if the file has been downloaded OK. */
	ecrire_fichier($parametres['xml_full_path'],$ma_page);
	return 20;
}

// enregistrer les erreurs dans le spip log
function comarquage_error($sError, $iType = E_USER_WARNING){
  spip_log('[ServicePublic] '.$iType.' '.$sError);
}

// verifier la disponibilite d'un processeur xsl
function comarquage_processeur_disponible(){
	static $_available = NULL;
	static $_executable = 'xsltproc';
	
	// on ne verifie qu'une fois a chaque hit
	if ($_available === NULL){
		foreach (explode(':', getenv('PATH')) as $sPath) {
			if (function_exists('is_executable'))
				if (is_executable($sPath.'/'.$_executable))
					$_available = TRUE;
		}
		
		if ($_available !== TRUE){
			comarquage_error("l'executable $_executable n'a pas ete ".
			"trouvé dans le PATH ('".getenv('PATH')."')");
			$_available = FALSE;
		}
	}
	return $_available;
}

?>