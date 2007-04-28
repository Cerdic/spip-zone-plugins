<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/noisetier_gestion');
include_spip('options_noisetier');

function exec_noisetier_dist(){
	global $spip_lang_right;
	global $theme_titre, $theme_zones;
	$theme_zones['head']['nom'] = "head";
	$theme_zones['head']['titre'] = _T('noisetier:head_titre');
	$theme_zones['head']['descriptif'] = _T('noisetier:head_descriptif');
	$theme_zones['head']['insere_avant'] = "<div style='width:100%'>";
	$theme_zones['head']['insere_apres'] = "</div>";

	pipeline('exec_init',array('args'=>array('exec'=>'noisetier'),'data'=>''));

	//Affichage de la page
	debut_page(_T('noisetier:titre_noisetier'));

	debut_gauche();
	debut_boite_info();
		echo "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>" ;
		echo _T('noisetier:theme_en_cours');
		echo "<br /><span style='font-size:large;'>";
		echo "$theme_titre";
		echo '</span></div>';
	fin_boite_info();
	pipeline('affiche_gauche',array('args'=>array('exec'=>'noisetier'),'data'=>''));

	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'noiseteir'),'data'=>''));

	debut_droite();
	gros_titre(_T('noisetier:titre_noisetier'));
	echo typo(_T('noisetier:presentation_noisetier')) ;
	echo '<br /><br />';

	noisetier_gestion_zone('head');
	foreach ($theme_zones as $theme_une_zone){
		//La zone head a déjà été insérée
		if ($theme_une_zone['nom']!='head')
			noisetier_gestion_zone($theme_une_zone['nom']);
	}


	echo fin_gauche(), fin_page();
}

?>