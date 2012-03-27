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

?>