<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Definition des balises :
* - #GEOPORTAIL_ENGINE{#ID_GEOPORTAIL} : inclusion unique des scripts du geoportail
* - #MAPKEY : renvoie la cle de l'API
* - #GEOPORTAIL_RGC : renvoie le rgc utilise, #GEOPORTAIL_RGC("by") renvoie la valeur du fichier de lang pour ce RGC.
* - #GEOPORTAIL_PROTECT(action) : demande un hash code pour action securisee 
*		-> necessite un appel a geoportail_good_referer(action) pour validation
*
* Definition du critere geoposition pour la jointure avec les tables SPIP
*
**/
// La version de l'API Geoportail qu'on utilise
define('_API_GEOPORTAIL_VERSION', "1.2");

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
include_spip('inc/config');

/** Affichage de la cle */
// Cle geoportail
function geoportail_key()
{	return "v="._API_GEOPORTAIL_VERSION."-e&key=".$GLOBALS['meta']['geoportail_key'];
}

// La Cle
function balise_MAPKEY($p)
{	// Code...
	$p->code = "geoportail_key()";
	$p->interdire_scripts = false;
	return $p;
}

/** Affichage du systeme pour la saisie des coordonnees */
// Cle geoportail
function geoportail_syscoord()
{	return $GLOBALS['meta']['geoportail_sysref'];
}

// La Cle
function balise_SYSCOORD($p)
{	// Code...
	$p->code = "geoportail_syscoord()";
	$p->interdire_scripts = false;
	return $p;
}

/** Affichage du RGC utilise */
function geoportail_rgc($p=null)
{	if ($p=="par") 
	{	if ($GLOBALS['meta']['geoportail_rgc']) return _T('geoportail:rgc_par_'.$GLOBALS['meta']['geoportail_rgc']);
		else return '';
	}
	return $GLOBALS['meta']['geoportail_rgc'];
}

// RGC utilise
function balise_GEOPORTAIL_RGC($p)
{	$param = interprete_argument_balise(1,$p);
	// Code...
	$p->code = "geoportail_rgc($param)";
	$p->interdire_scripts = false;
	return $p;
}

/** Protection des scripts */
// Cle geoportail
function geoportail_protect($action=null)
{	charger_fonction('securiser_action','inc');
	return calculer_action_auteur($action);
}

// Protection des scripts
function balise_GEOPORTAIL_PROTECT($p)
{	// faire un cache par session
	$p->descr['session'] = true; 
	// Parametre
	$param = interprete_argument_balise(1,$p);
	// Code...
	$p->code = "geoportail_protect($param)";
	$p->interdire_scripts = false;
	return $p;
}

/**
* #GEOPORTAIL_ENGINE{#ID_GEOPORTAIL}
* La balise doit necessairement contenir l'identifiant de la carte 
* pour n'ajouter l'engin qu'une seule fois
*
**/
global $geoportail_engine;
$geoportail_engine=-1;

function geoportail_engine($id_geoportail)
{	global $geoportail_engine;
	if ($geoportail_engine == -1 || $geoportail_engine == $id_geoportail) 
	{	$geoportail_engine = $id_geoportail;
		$engine = '<script type="text/javascript" src="http://api.ign.fr/geoportail/api/js/'._API_GEOPORTAIL_VERSION.'/GeoportalExtended.js" charset=utf-8><!-- --></script>'
				. '<script language=javascript>jQuery(document).ready(	function() { jQuery.geoportail.initMap("'._DIR_PLUGIN_GEOPORTAIL.'"); });</script>'
				;
		return $engine; 
	}
	else return '';
}

function balise_GEOPORTAIL_ENGINE ($p) 
{	$param = interprete_argument_balise(1,$p);
	$p->code = "geoportail_engine($param)";
	$p->interdire_scripts = false;
	return $p;
}

?>