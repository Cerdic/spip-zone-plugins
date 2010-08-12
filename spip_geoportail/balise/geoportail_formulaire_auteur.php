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
* Formulaire public de localisation des auteurs 
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_GEOPORTAIL_FORMULAIRE_AUTEUR_dist ($p) 
{	
	return calculer_balise_dynamique($p,'GEOPORTAIL_FORMULAIRE_AUTEUR', array($objet,$id));
}

function balise_GEOPORTAIL_FORMULAIRE_AUTEUR_stat($args, $filtres) 
{	
	return $args;
}

function balise_GEOPORTAIL_FORMULAIRE_AUTEUR_dyn($objet='', $id_objet='', $titre='', $deplier=false) 
{	// Formulaire public de localisation des auteurs connectes ?
	$objet='auteur';
	$id_objet=$GLOBALS['auteur_session']['id_auteur'];
	return array(
       'formulaires/geoportail_formulaire',
       0,
       array(
         'id_objet'		=> $id_objet ? $id_objet:0,
         'objet'		=> $objet ? $objet:'erreur',
         'titre'		=> $titre,
         'deplier'		=> $deplier ? ' ':'',
         'lon'	=> _request('lon'),
         'lat'	=> _request('lat'),
         'zone'	=> _request('zone'),
         'zoom'	=> _request('zoom')
       )
   );
}

?>