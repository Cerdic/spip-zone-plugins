<?php 

include_spip('inc/filtres');
//Declaration globale des nouveaux statuts personnalises
$statuts=statut_auteurs_get_statuts();
foreach ($statuts as $statut=>$libelle)
	$GLOBALS['liste_des_statuts'][$statut]=$statut; //extraire_multi($libelle); 
	
	// spip utilise la valeur de ce tableau pour en faire des traductions...
	//on est donc obliges de ce conformer a ce schema la

	
	
	
function statut_auteurs_get_statuts(){
	
		$statuts_installes = @unserialize($GLOBALS['meta']['statut_auteurs:autre_statut_auteur']);
		if (!is_array($statuts_installes)) $statuts_installes = array();
		return $statuts_installes;
}	
	
?>