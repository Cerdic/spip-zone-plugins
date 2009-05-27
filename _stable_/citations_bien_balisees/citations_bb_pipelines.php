<?php
// plugin Citations bien balisees
// CopyLeft 2009 Bertrand Marne citations_bb à sciencesnat point org
// GPL v3

// Modification du flux pour remplacer <quote> par <q> quand
// il n'y a pas de retour à la ligne (preg sans l'option s !)
function citations_bb_pre_propre($flux){
$flux=preg_replace("/(<quote>)(.*?)(<\/quote>)/","<q>\$2</q>",$flux);
	return $flux;
}
// Modification du flux pour ajouter des CSS pour les <q>
// dans la partie privée
function citations_bb_header_prive($flux) {
	$flux.= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/citations_bb.css') . '" />' . "\n";
	return $flux;
}
// Modification du flux pour ajouter des CSS pour les <q>
// dans la partie publique
function citations_bb_insert_head($flux) {
	$flux.= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/citations_bb.css') . '" />' . "\n";
	return $flux;
}

?>