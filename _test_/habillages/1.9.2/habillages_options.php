<?php
include_spip('inc/meta');

# TODO : ajout d'un repertoire de personnalisation des squelettes (les 
# repertoires classiques) pour permettre le bidouillage des squelettes.
# Ceci doit venir en plus d'une interface de personnalisation des dossiers
# de squelettes et de themes.
lire_metas();
$lire_habillages_squelettes = $GLOBALS['meta']['habillages_squelettes'];
$lire_habillages_themes = $GLOBALS['meta']['habillages_themes'];

if ($lire_habillages_squelettes == "dist" && $lire_habillages_themes == "defaut") {
	$habillages_dossiers_squelettes = "dist";
	$GLOBALS['dossier_squelettes'] = $habillages_dossiers_squelettes;
}
else if ($lire_habillages_squelettes == "dist" && $lire_habillages_themes != "defaut") {
	$habillages_dossiers_squelettes = 'plugins/'.$lire_habillages_themes.'/:dist';
 	$GLOBALS['dossier_squelettes'] = $habillages_dossiers_squelettes;
}
else {
	$GLOBALS['dossier_squelettes'] = 'plugins/'.$lire_habillages_squelettes.'/';
}
?>