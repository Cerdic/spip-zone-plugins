<?php

$GLOBALS['dossier_squelettes'] = 'dist';

/*
include_spip('inc/meta');

lire_metas();
$lire_habillages_squelettes = $GLOBALS['meta']['habillages_squelettes'];
$lire_habillages_prefixe_squel = $GLOBALS['meta']['habillages_prefixe_squel'];
$squelette_reperso = $GLOBALS['meta']['habillages_'.$lire_habillages_prefixe_squel.'_reperso'];
$lire_habillages_themes = $GLOBALS['meta']['habillages_themes'];

if ($lire_habillages_squelettes == "dist" && $lire_habillages_themes == "defaut") {
	$habillages_dossiers_squelettes = "dist";
	$GLOBALS['dossier_squelettes'] = $habillages_dossiers_squelettes;
}
else if ($lire_habillages_squelettes == "dist" && $lire_habillages_themes != "defaut") {
	$habillages_dossiers_squelettes = 'plugins/'.$lire_habillages_themes.'/:dist';
 	$GLOBALS['dossier_squelettes'] = $habillages_dossiers_squelettes;
}
else if ($lire_habillages_squelettes == "defaut") {
}
else {
	$GLOBALS['dossier_squelettes'] = $squelette_reperso.':plugins/'.$lire_habillages_squelettes.'/';
}
*/
/*
+--------------------------------------------+
| ICOP 1.0 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| surcharge options : declare couleurs
+--------------------------------------------+
*/

#
# nouvelles couleurs ICOP
# 
$GLOBALS['mes_couleurs'] = array(
// bleu koak
1 => array(
	"couleur_foncee" => "#678B9D",
	"couleur_claire" => "#B4CFDB",
	"couleur_lien" => "#416575",
	"couleur_lien_off" => "#5C8193"
	),
// brun koak
2 => array (
	"couleur_foncee" => "#9C7C57",
	"couleur_claire" => "#CFBBA3",
	"couleur_lien" => "#764D1E",
	"couleur_lien_off" => "#936E43"
	),
// mauve koak
3 => array(
	"couleur_foncee" => "#B57BB6",
	"couleur_claire" => "#DEB2DF",
	"couleur_lien" => "#6D0D6E",
	"couleur_lien_off" => "#942A95"
	),
// kaki koak
4 => array(
	"couleur_foncee" => "#808A47",
	"couleur_claire" => "#B5BC8D",
	"couleur_lien" => "#5B6427",
	"couleur_lien_off" => "#5B6427"
	),
// vert-eau koak
5 => array(
	"couleur_foncee" => "#358B8A",
	"couleur_claire" => "#89B9B8",
	"couleur_lien" => "#1C5D5C",
	"couleur_lien_off" => "#1F7A79"
	),
// sang koak
6 => array(
	"couleur_foncee" => "#AF2728",
	"couleur_claire" => "#E27879",
	"couleur_lien" => "#7C0D0E",
	"couleur_lien_off" => "#A11012"
	),
// orange koak
7 => array(
	"couleur_foncee" => "#D4700C",
	"couleur_claire" => "#EEB04D",
	"couleur_lien" => "#A93902",
	"couleur_lien_off" => "#C26202"
	),
// monochrome koak
8 => array(
	"couleur_foncee" => "#848485",
	"couleur_claire" => "#B5B9BC",
	"couleur_lien" => "#555556",
	"couleur_lien_off" => "#717374"
	),
// mais koak
9 => array(
	"couleur_foncee" => "#AC9B04",
	"couleur_claire" => "#E2D77E",
	"couleur_lien" => "#7B6E02",
	"couleur_lien_off" => "#9B8C09"
	)
);


#
# Surcharge couleurs perso
#
include_spip('mes_couleurs');


#
# charger la sélection nouv. coul. dans spip
# 
if(!function_exists('lire_metas')) {
	include_spip('inc/meta');
}
lire_metas();

if($GLOBALS['meta']['habillages_couleurs']!=''){
	$tb_coul=explode(',',$GLOBALS['meta']['habillages_couleurs']);
	foreach($tb_coul as $nc) {
		$GLOBALS['couleurs_spip'][]=$mes_couleurs[$nc];
	}
}


?>