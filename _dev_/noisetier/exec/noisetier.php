<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/noisetier_gestion');
include_spip('options_noisetier');

function exec_noisetier_dist(){
	global $spip_lang_right;
	global $theme_titre, $theme_zones, $noisetier_pages, $noisetier_description_pages, $page;

	if (!isset($page)) $page='';

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
	echo "<br />";
	pipeline('affiche_gauche',array('args'=>array('exec'=>'noisetier'),'data'=>''));

	creer_colonne_droite();
	debut_boite_info();
		if ($page!='') {
			echo "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>" ;
			echo _T('noisetier:page_affichee');
			echo "<br /><span style='font-size:large;'>";
			echo "$page";
			echo '</span></div>';
			if (isset($noisetier_description_pages[$page])) echo propre($noisetier_description_pages[$page]);
		}
		echo _T('noisetier:restreindre_page');
		echo "<div style='text-align:center;'>";
		echo "<form method='get' action='".generer_url_ecrire('noisetier')."'>";
		echo "<input type='hidden' name='exec' value='noisetier' />";
		echo "<select name='page' class='verdana1 toile_foncee' style='max-height: 24px; border: 1px solid white; color: white; width: 100px;'>";
		if ($page!='') echo "<option value=''>"._T('noisetier:voir_toutes_noisettes')."</option>";
		if ($page!='toutes') echo "<option value='toutes'>"._T('noisetier:toutes')."</option>";
		asort($noisetier_pages);
		foreach ($noisetier_pages as $unepage)
			if($unepage!=$page) echo "<option value='$unepage'>$unepage</option>";
		echo "</select><input type='submit' class='fondo' value='"._T('noisetier:changer')."'/></form></div>";
	fin_boite_info();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'noiseteir'),'data'=>''));


	debut_droite();
	gros_titre(_T('noisetier:titre_noisetier'));
	echo typo(_T('noisetier:presentation_noisetier')) ;
	echo '<br /><br />';

	noisetier_gestion_zone('head', $page, true);
	foreach ($theme_zones as $theme_une_zone){
		//La zone head a d�j� �t� ins�r�e
		if ($theme_une_zone['nom']!='head')
			//Restriction en fonction du param�tre page
			if(noisetier_affiche_zone_page($theme_une_zone,$page))
				noisetier_gestion_zone($theme_une_zone['nom'],$page);
	}

	//Afficher ici les zones non g�r�es par le th�me en cours mais qui disposent n�anmoins d'une d�claration dans la base de donn�e

	echo fin_gauche(), fin_page();
}

// d�termine si une zone donn�e doit �tre affich�e sur une page donn�e
function noisetier_affiche_zone_page($theme_une_zone,$page) {
	if ($page=='') return true;
	if (isset($theme_une_zone['pages_exclues']) && preg_match("/(^|,)".$page."(,|$)/",$theme_une_zone['pages_exclues']))
		return false;
	if (isset($theme_une_zone['pages']) 
		&& !preg_match("/(^|,)".$page."(,|$)/",$theme_une_zone['pages'])
		&& !preg_match("/(^|,)toutes(,|$)/",$theme_une_zone['pages']))
		return false;
	return true;
}

?>