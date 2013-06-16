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
* Autorisation pour le positionnement des auteurs
*
**/

/** Affichage du logo des geoservice
*/
$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_service');

/** Pipeline autoriser pour geoportail
*/
function geoportail_autoriser($flux) { return $flux; }

/** Autorisation de modifier le logo d'un geoservice.
*/
function autoriser_geoservice_iconifier ($faire, $type, $id, $qui, $opt)
{	return ($qui['statut'] == '0minirezo');
}

/**
* API d'autorisation
* Un auteur peut modifier son geopositionnement
*/
function autoriser_auteur_positionner_dist ($faire, $type, $id, $qui, $opt)
{	return 
	(	(($qui['statut'] == '0minirezo') && !$qui['restreint'])
	||	($qui['id_auteur'] == $id)
	);
}

/** Autorisation pour le geoproxy
	=> On n'autorise que les urls dans la variable globale $GLOBALS['geoportail_url_autorisees']
*/
function autoriser_geoproxy_dist ($faire, $type, $id, $qui, $opt)
{	// Table des sites autorises (dans fichier Options)
    $geoportail_url_autorisees = $GLOBALS['geoportail_url_autorisees'];
    if (!$geoportail_url_autorisees OR !is_array($geoportail_url_autorisees))
		$geoportail_url_autorisees = array();
	// Pour les recherches par adresses (obsolete)
	$url_autorisees[] = "http://wxs.ign.fr/";
	
	$url = urldecode($opt['url']);
	for ($i=0; $i<sizeof($url_autorisees); $i++)
	{	if (substr($url, 0, strlen($url_autorisees[$i])) == $url_autorisees[$i]) return true;
	}
	return false;
}

?>