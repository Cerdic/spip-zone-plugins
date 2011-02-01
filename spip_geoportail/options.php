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
* Options utiles + autorisation pour le positionnement des auteurs
*
**/

// Affichage logo du service
if ($GLOBALS['spip_version_code'] > 1.99) 
{	charger_fonction('iconifier', 'inc');
	$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_service');
}

/**
* API d'autorisation
* Un auteur peut modifier son geopositionnement
*/
function autoriser_auteur_positionner ($faire, $type, $id, $qui, $opt)
{	return 
	(	(($qui['statut'] == '0minirezo') && !$qui['restreint'])
	||	($qui['id_auteur'] == $id)
	);
}

?>