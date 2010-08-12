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
* Definition du formulaire geoportail
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_GEOPORTAIL_FORMULAIRE_dist ($p) 
{
	return calculer_balise_dynamique($p,'GEOPORTAIL_FORMULAIRE', array($objet,$id));
}

function balise_GEOPORTAIL_FORMULAIRE_stat($args, $filtres) 
{
	if (!$args[0]) return "GEOPORTAIL_FORMULAIRE en dehors d'une boucle objet.";
	return $args;
}

function balise_GEOPORTAIL_FORMULAIRE_dyn($objet='', $id_objet='', $titre='', $deplier=false) 
{	return array(
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