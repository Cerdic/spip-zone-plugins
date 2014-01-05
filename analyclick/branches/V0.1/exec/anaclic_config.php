<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Configuration des stats de telechargement 
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_anaclic_config_dist()
{	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('icone_configuration_site'), "anaclic_config", "anaclic");
	echo gros_titre(_T('anaclic:statistiques_documents'),'', false);

	echo debut_gauche('', true);
	echo creer_colonne_droite('', true);
	
	if (autoriser('configurer'))
	{	if ($GLOBALS['spip_version_branche']>2)
			$res = icone_horizontale(_T('anaclic:statistiques_documents'), generer_url_ecrire("statistiques_anaclic_v3"), "statistiques-24.gif","rien.gif", false);
		else $res = icone_horizontale(_T('anaclic:statistiques_documents'), generer_url_ecrire("statistiques_anaclic"), "statistiques-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);
	}
	
	$delai = (isset($GLOBALS['meta']['anaclic_delai']) ? $GLOBALS['meta']['anaclic_delai'] : 3600 );
	
	echo debut_droite('', true);

	echo debut_cadre_trait_couleur ("statistiques-24.gif", true, "", _T('anaclic:configurer'));
	echo debut_cadre_relief("",true)
		._T('anaclic:configurer_info')
		.fin_cadre_relief(true);

	$form = "<label for='delai'>"._T('anaclic:delais')." : </label><input class='fondl' type='text' name='delai' id='delai' size='20' value='$delai'>"
		."<input class='fondo' type='submit' name='modifier' style='margin-left:1em;' value='"._T('bouton_valider')."'>";
	// Formulaire
	echo generer_action_auteur('anaclic_config',
		'geoportail_config',
		'./?exec=anaclic_config',
		$form,
		" method='post' name='formulaire'"
	);

	echo fin_cadre_relief(true);
	
	echo fin_gauche(), fin_page();	
}

?>