<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Affichage de la liste des geoservice
*
**/

include_spip('inc/presentation');
include_spip('inc/config');

function exec_geoservice_tous()
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('geoportail:geoservice'), "", "");

	echo pipeline('exec_init',array('args'=>array('exec'=>'geoservice_tous'),'data'=>''));

	echo debut_gauche('', true);

	echo debut_cadre_trait_couleur("", true);
	echo "<p class='arial1'>"._T('geoportail:info_geoservice')."</p>";
	echo "<p class='arial1'>"._T('geoportail:info_geoportail_service')."</p>";
	echo fin_cadre_trait_couleur(true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'geoservice_tous'),'data'=>''))
	.creer_colonne_droite('', true)
	.pipeline('affiche_droite',array('args'=>array('exec'=>'geoservice_tous'),'data'=>''))
	.debut_droite('',true);
	
	echo gros_titre(_T('geoportail:geoservice'), "", false);
	echo geoportail_table_geoservices ();
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'geoservice_tous'),'data'=>''));

	echo fin_gauche();
	echo fin_page();
}

?>