<?php
include_spip('inc/meta');

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