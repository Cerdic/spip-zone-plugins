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
* Configuration du plugin
*
**/

include_spip('inc/presentation');
include_spip('inc/config');

function exec_geoportail_importrgc()
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('geoportail:geoportail'), "", "");

	echo debut_gauche('',true);
	echo debut_droite('',true);
	echo gros_titre("<p>Plugin "._T('geoportail:geoportail')."</p>", '', false);
	
 	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;
 	
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	// On est OK
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
		$rgc = $GLOBALS['meta']['geoportail_rgc'];
		if ($row || !$rgc) echo _T('geoportail:import_double');	
		else
		{
			echo _T('geoportail:import_rgc')." : $rgc<br/>";
			echo recuperer_fond ('fonds/import_rgc', array ('couleur_foncee' => $couleur_foncee ) ); 
		}
	}
	else
	{	// Pas d'acces
		echo "<br/><br/>".gros_titre(_T('avis_non_acces_page'));
	}

	echo fin_gauche();
	echo fin_page();
}

?>