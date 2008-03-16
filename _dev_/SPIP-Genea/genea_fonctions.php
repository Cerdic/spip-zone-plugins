<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	* fichier : genea_fonctions.php
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

//include_spip('public/genea_boucles');
include_spip('inc/genea_filtres');
include_spip('public/genea_balises');

// -- Definition de la table permettant l'affichage du logo d'un individu
include_spip('inc/chercher_logo');
global $table_logos;
$table_logos['id_individu'] = 'indiv';

function balise_GENEA_VERSION_PLUGIN(){
	include_spip('inc/plugin');
	$infos=plugin_get_infos(_DIR_PLUGIN_GENEA);
	return $infos['version'];
}

function balise_GENEA_VERSION_BASE(){
	include_spip('base/genea_base');
	return $GLOBALS['version_base_genea'];
}
?>