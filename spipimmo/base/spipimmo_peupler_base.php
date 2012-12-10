<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function peupler_base_spipimmo() {

	sql_insertq_multi('spip_spipimmo_types_offres', array(
		array('id_type_offre'=>'1','libelle_offre'=>'<multi>[fr]Appartement</multi>'),
		array('id_type_offre'=>'2','libelle_offre'=>'<multi>[fr]Boutique</multi>'),
		array('id_type_offre'=>'3','libelle_offre'=>'<multi>[fr]Bureaux</multi>'),
		array('id_type_offre'=>'4','libelle_offre'=>'<multi>[fr]Bureau / Local commercial</multi>'),
		array('id_type_offre'=>'5','libelle_offre'=>'<multi>[fr]Commerce</multi>'),
		array('id_type_offre'=>'6','libelle_offre'=>'<multi>[fr]Divers</multi>'),
		array('id_type_offre'=>'7','libelle_offre'=>'<multi>[fr]Hangar</multi>'),
		array('id_type_offre'=>'8','libelle_offre'=>'<multi>[fr]Hôtel particulier</multi>'),
		array('id_type_offre'=>'9','libelle_offre'=>'<multi>[fr]Immeuble</multi>'),
		array('id_type_offre'=>'10','libelle_offre'=>'<multi>[fr]Local</multi>'),
		array('id_type_offre'=>'11','libelle_offre'=>'<multi>[fr]Maison / Villa</multi>'),
		array('id_type_offre'=>'12','libelle_offre'=>'<multi>[fr]Parking</multi>'),
		array('id_type_offre'=>'13','libelle_offre'=>'<multi>[fr]Terrain</multi>'),
		)
	);
}
?>
