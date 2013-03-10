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
* Forumlaire d'edition d'un geoservice
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/auth');
include_spip("inc/lang");
include_spip("base/abstract_sql");
include_spip('inc/compat_192');
include_spip('inc/geoportail_autorisations');

function action_geoservice_edit_dist() 
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_geoservice = intval($arg);
	
	// Attributs du service
	if (isset($_POST['valider']))
	{	$id_rubrique = _request('id_parent');
		if (!$id_rubrique) $id_rubrique=0;
		$titre = addslashes(_request('titre'));
		$descriptif = addslashes(_request('descriptif'));
		$type = _request('type');
		$url = addslashes(_request('url'));
		$map = addslashes(_request('map'));
		$layers = _request('layers');
		$format = _request('format');
		$niveau = intval(_request('niveau'));
		$maxextent = _request('extent');
		$minzoom = _request('minzoom');
		$maxzoom = _request('maxzoom');
		$opacity = _request('opacity');
		$zone = _request('zone');
		$visibility = isset($_POST['visibility']);
		$select = isset($_POST['select']);
		//$logo = eregi_replace("[^a-z0-9]",'_',_request('logo'));
		$logo = translitteration(_request('logo'));
		$logo = eregi_replace("[^a-z0-9]",'_',$logo);

		$link = addslashes(_request('link'));
		
		if (!$titre) $titre = _T("ecrire:info_sans_titre");
		
		// Modifier ?
		if ($id_geoservice)
		{	if (autoriser('modifier','geoservice',$id_geoservice, NULL, array('id_rubrique'=>$id_rubrique)))
			{	spip_query("UPDATE spip_geoservices SET "
					."id_rubrique='$id_rubrique',type='$type',titre='$titre',descriptif='$descriptif',url_geoservice='$url',zone='$zone',map='$map',layers='$layers',format='$format',niveau='$niveau',maxextent='$maxextent',minzoom='$minzoom',maxzoom='$maxzoom',opacity='$opacity',visibility='$visibility',selection='$select',logo='$logo',link='$link' "
					."WHERE id_geoservice=$id_geoservice");
			}
		}
		// Nouveau service demande
		elseif (autoriser ('creer', "geoservice", $id_geoservice, NULL, array('id_rubrique'=>$id_rubrique))) 
			$id_geoservice = sql_insert("spip_geoservices", 
				"(id_rubrique,type,titre,descriptif,url_geoservice,zone,map,layers,format,niveau,maxextent,minzoom,maxzoom,opacity,visibility,logo,link)", 
				"('$id_rubrique','$type','$titre','$descriptif','$url','$zone','$map','$layers','$format','$niveau','$maxextent','$minzoom','$maxzoom','$opacity','$visibility','$logo','$link')"
				);
	}	
	// Modifier le statut d'un service
	elseif (isset($_POST['valider_statut']) && $id_geoservice)
	{	if (autoriser('publier','geoservice',$id_geoservice, NULL, $row))
 		{	$statut = _request('statut');
			spip_query("UPDATE spip_geoservices SET statut='".$statut."' WHERE id_geoservice=".$id_geoservice);
		}
	}
	
	// Rediriger le navigateur
	$redirect = parametre_url(urldecode(_request('redirect')),
		'id_geoservice', $id_geoservice, '&');

	redirige_par_entete(_DIR_RESTREINT.$redirect);

}

?>