<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Insertion du formulaire de saisie des coordonnées dans l'espace privé
 *
 */
 
include_spip('base/abstract_sql');
include_spip('inc/autoriser');

// Affichage de la carte dans la partie privée
function gmap_saisie_privee($id, $table, $exec, $deplie = 0)
{
    // Clef primaire de la table (rubrique, article ou document)
	$pkey = id_table_objet($table);
	$id = intval($id);
	$flux = "";
	
	// on recupere l'id de l'auteur en cours
	if ($GLOBALS["auteur_session"])
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	// et on verifie qu'il est autorisé à  modifier l'élément en cours
	if (!autoriser("modifier",$table,$id))
		return $flux;
	
	// Modification de la géolocalisation
	$formulaire = charger_fonction('geolocaliser', 'formulaires');
	if ($formulaire)
		$flux .= $formulaire($id, $table, $exec, $deplie);
	
	return $flux;
}
	
?>