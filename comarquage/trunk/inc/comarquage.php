<?php

/* Plugin Comarquage -flux V2- pour SPIP 1.9
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


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

include_spip('inc/config');


$local_refresh = lire_config('comarquage/local_refresh');
if (!isset($local_refresh)) {
	ecrire_config('comarquage/local_refresh','259200'); /* 60*60*24*3 */
}
$local_timeout = lire_config('comarquage/local_timeout');
if (!isset($local_timeout)){
	ecrire_config('comarquage/local_timeout','604800'); /* 60*60*24*7 */
}

$default_xml_file = lire_config('comarquage/default_xml_file');
if (!isset($default_xml_file)){
	ecrire_config('comarquage/default_xml_file','Themes.xml');
}

$default_xsl_file = lire_config('comarquage/default_xsl_file');
if (!isset($default_xsl_file)){
	ecrire_config('comarquage/default_xsl_file','spThemes.xsl');
}


// recuperer le contenu compile d'une page xml
function & comarquage_compile_page_xml($parametres,$url_base){
	global $type_urls;
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
	if (!comarquage_prepare_fichiers_xml($parametres))  {
		spip_log("Erreur du rapatriement des fichiers",'comarquage');
		return -20;

	}


	/*----------------------------------------
	 *  definir les parametres xsl
	 */
	$parametres_xsl = array();
	$parametres_xsl['IMAGESURL'] = $parametres_xsl['SITEURL'].'/'._DIR_PLUGIN_COMARQUAGE_IMAGES;

	if (isset($parametres['lettre']))	$parametres_xsl['LETTRE'] = $parametres['lettre'];
	if (isset($parametres['motcle'])) $parametres_xsl['MOTCLE'] = $parametres['motcle'];

	/* Réglage pour l'URL */
	$parametres_xsl['REFERER'] = $GLOBALS['REQUEST_URI'];
	// spip_log("REFERER 1 : ".$parametres_xsl['REFERER'],"comarquage");
	$parametres_xsl['REFERER'] = parametre_url($parametres_xsl['REFERER'],"var_mode",'','&');
	$parametres_xsl['REFERER'] = parametre_url($parametres_xsl['REFERER'],"xml",'','&'); // on enlève les paramètres d'url
	// spip_log("REFERER 2 : ".$parametres_xsl['REFERER'],"comarquage");
	$parametres_xsl['REFERER'] = parametre_url($parametres_xsl['REFERER'],"xsl",'','&');
	// spip_log("REFERER 3 : ".$parametres_xsl['REFERER'],"comarquage");
	//$parametres_xsl['REFERER'] = $GLOBALS['REQUEST_URI'].'?&' ; // url principale du comarquage

	// Si un autre jeu d'URL est utilisé (propre), on ajoute l'esperluette
	if ($type_urls == "page") $parametres_xsl['REFERER'] .= '&';
	else $parametres_xsl['REFERER'] .= '?&';


	// MODIF VI :  REFERER / PICTOS / SITEURL / IMAGES / PIVOTS / XMLURL / CATEGORIE
	$parametres_xsl['SITEURL'] = lire_meta("adresse_site" );
	$parametres_xsl['PICTOS'] = $parametres_xsl['SITEURL'].'/'._DIR_PLUGIN_COMARQUAGE_IMAGES; // url des picto (web, téléphone, ...)
	$parametres_xsl['IMAGES'] = $parametres_xsl['SITEURL'].'/'._DIR_PLUGIN_COMARQUAGE_IMAGES.'/'; // URL des images
	$parametres_xsl['PIVOTS'] = 'mairie'; // pivots locaux
	$parametres_xsl['XMLURL'] = 'http://lecomarquage.service-public.fr/xml2v2/'; // url des données XML du comarquage
	$parametres_xsl['CATEGORIE'] = $parametres['categorie']; // particuliers, associations ou entreprises

	switch ($parametres_xsl['CATEGORIE']) {
		case "particuliers":
			$parametres_xsl['XMLURL'] = "http://lecomarquage.service-public.fr/xml2v2/";
		break;

		case "associations":
			$parametres_xsl['XMLURL'] = "http://lecomarquage.service-public.fr/xmlassov2/";
		break;

		case 'entreprises':
			$parametres_xsl['XMLURL'] = "http://lecomarquage.service-public.fr/xmlpmev2/";
		break;

		default:
			$parametres_xsl['XMLURL'] = "http://lecomarquage.service-public.fr/xml2v2/";
		break;
	}

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


// recuperer toutes les ressouces associees a un fichier xml,  inutile pour les flux v2
// dans un tableau
function comarquage_extraire_ressources($fichier_xml, $ma_page){
	$liste_ressources=array();
	include_spip('inc/plugin');
	include_spip('inc/filtres');
	include_spip('inc/xml');
	$arbre = spip_xml_parse($ma_page);
	if (is_array($arbre)){
		$arbre = reset($arbre); // prendre le noeud racine
		$arbre = $arbre[0];
		if (isset($arbre['Fils']))
			foreach($arbre['Fils'] as $subtree)
				foreach($subtree as $tag=>$val){
					$f = extraire_attribut("<$tag>",'lien');
					$f = basename($f,'.xml').'.xml';
					$liste_ressources[]=$f;
				}
	}
	// spip_log("*********************** DEBUT liste ressource","comarquage");
	// spip_log($liste_ressources,"comarquage");
	// spip_log("*********************** FIN liste ressource","comarquage");
	return $liste_ressources;
}

function & comarquage_transforme_fichier_xml($fichier_xml, $fichier_xsl = NULL, $parametres = NULL){
	static $_executable = 'xsltproc';


	$params = " --path "._DIR_CACHE._DIR_CACHE_COMARQUAGE_XML."/ ";
	if (is_array($parametres))
		foreach ($parametres as $k => $v) {
			$params .= '--stringparam '.escapeshellarg($k).' '.escapeshellarg($v).' ';
		}
//		 spip_log("<br><br>\n\nPAR'AM : $params","comarquage");


	$fichier_erreur = tempnam('/tmp', 'xsltprocErrors_');
	$commande = $_executable . $params . ($fichier_xsl ? $fichier_xsl.' ' : '');
	$commande .= $fichier_xml;
	$commande .=  " 2> $fichier_erreur";

	// spip_log("commande XSLTPROC : ".$commande,"comarquage");

	exec($commande, $retour, $erreur_code);

  	comarquage_error($commande);

	if (filesize($fichier_erreur)) {
		lire_fichier($fichier_erreur,$message);
		comarquage_error("la commande '$_executable $params' a retourné ($erreur_code) : $message");
	}

	unlink($fichier_erreur);
	return $erreur_code ? FALSE : $retour;
}

// definir le nom du fichier de stockage de la page en cache
function comarquage_prepare_parametres_cache(& $parametres,$url_base){
	spip_log("Paramètre avant comarquage_prepare_parametres_cache","comarquage");
	spip_log($parametres,"comarquage");

	$cache_id = '';
	if (isset($parametres['lettre'])) $cache_id .= 'l'.$parametres['lettre'];
	if (isset($parametres['motcle'])) $cache_id .= 'm'.md5($parametres['motcle']);
	$cache_id .= md5($url_base.$parametres['categorie']);

	$parametres['cache_full_path'] = sous_repertoire(_DIR_CACHE,_DIR_CACHE_COMARQUAGE_CACHE).
		basename($parametres['xml'], '.xml').
		($cache_id ? '.'.$cache_id : '').'.cache';

		spip_log("Paramètre APRES comarquage_prepare_parametres_cache","comarquage");
		spip_log($parametres,"comarquage");
}

// lire le fichier xml parse en cache
function & comarquage_lire_cache($parametres) {
	$fichier = $parametres['cache_full_path'];
	if (file_exists($fichier)
		&& ($t = filemtime($fichier))
		&& (time()-$t < $GLOBALS['meta']['comarquage_local_timeout'])
		&& ($t > filemtime($parametres['xml_full_path']))
		&& ($t > filemtime(dirname($parametres['xsl_full_path'])))
		) {

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

		// En fonction de la catégorie (particulier / associations / professionnels) l'url cible n'est pas la même
		switch ($parametres['categorie']) {
		case "particuliers":
			$url = "http://lecomarquage.service-public.fr/xml2v2/";
		break;

		case "associations":
			$url = "http://lecomarquage.service-public.fr/xmlassov2/";
		break;

		case 'entreprises':
			$url = "http://lecomarquage.service-public.fr/xmlpmev2/";
		break;

		default:
			$url = "http://lecomarquage.service-public.fr/xml2v2/";
		break;
		}
	//$url = $GLOBALS['meta']['comarquage_xml_server'];

	$url = $url.$parametres['xml'];
	spip_log("URL du flux : $url","comarquage");

	include_spip('inc/distant');
	$ma_page = recuperer_page($url);

	if ($ma_page===FALSE || !strlen($ma_page)) return FALSE;

	/* Return 20 if the file has been downloaded OK. */
	ecrire_fichier($parametres['xml_full_path'],$ma_page);

	/*
	spip_log('************************** DEBUT paramètres recuperer_page_xml',"comarquage");
	spip_log($parametres,"comarquage");
	spip_log('************************** FIN paramètres recuperer_page_xml',"comarquage");
	*/

	return 20;
}

// enregistrer les erreurs dans le spip log
function comarquage_error($sError, $iType = E_USER_WARNING){
  spip_log('[ServicePublic] '.$iType.' '.$sError,"comarquage");
}

// verifier la disponibilite d'un processeur xsl
function comarquage_processeur_disponible(){
	static $_available = NULL;
	static $_executable = 'xsltproc';

	// on ne verifie qu'une fois a chaque hit
	if ($_available === NULL){
		foreach (explode(':', getenv('PATH')) as $sPath) {
			//if (function_exists('is_executable'))
			//	if (is_executable($sPath.'/'.$_executable))
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
