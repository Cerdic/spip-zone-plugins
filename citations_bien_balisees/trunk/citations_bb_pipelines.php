<?php
// plugin Citations bien balisees
// CopyLeft 2009 Bertrand Marne citations_bb à sciencesnat point org
// GPL v3

// Modification du flux pour remplacer <quote> par <q> quand
// il n'y a pas de retour à la ligne (sur 3 niveaux, preg sans l'option s !)
function citations_bb_pre_propre($flux){
	if (strpos($flux, "<qu")===false) return $flux;
	$flux = preg_replace($a="/<quote>(.*?)<\/quote>/", $b="<q>\$1</q>", $flux);
	if (strpos($flux, "<qu")!==false) {
		$flux = preg_replace($a, $b, $flux);
		if (strpos($flux, "<qu")!==false) $flux = preg_replace($a, $b, $flux);
	}
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