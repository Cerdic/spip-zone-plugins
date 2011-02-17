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

function exec_geoportail_config_rgc()
{	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	// Administrateur global seulement
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('geoportail:geoportail'), "", "");
		echo gros_titre("Plugin "._T('geoportail:geoportail'), '', false);

		echo debut_gauche('',true);

		$res = icone_horizontale(_T('geoportail:cles'), generer_url_ecrire("geoportail_config"), "racine-site-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:options'), generer_url_ecrire("geoportail_config_options"), "administration-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:rgc'), generer_url_ecrire("geoportail_config_rgc"), "breve-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);

		echo debut_droite('',true);
		
		/* RGC */
		// Pas de RGC installe
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
		if (!$row) 
		{   effacer_meta('geoportail_rgc');
 					ecrire_metas();
			lire_metas();
		}

		$form = debut_cadre_trait_couleur("breve-24.gif", true, "", _T('geoportail:rgc'))
			."<p>"._T('geoportail:rgc_info')."</p>";
		// Scanner le repertoire rgc
		$dir = opendir(_FULLDIR_PLUGIN_GEOPORTAIL."rgc");
		$count = 0;
		while ($file = readdir($dir))
		{	if (is_dir($file)) continue;
			if (!preg_match("/^rgc\..*\.txt/", $file)) continue;
			$rgc = preg_replace (array("/^rgc\./","/\.txt/"),"", $file);
			if ($GLOBALS['meta']['geoportail_rgc'] == $rgc)
			{	$form .= debut_cadre_relief("", true)
					. "<b style='color:$couleur_foncee'>"._T('geoportail:rgc_use_'.$rgc)."</b><br/>"
					. _T('geoportail:rgc_info_'.$rgc)
					."<input type='submit' name='geoportail_norgc' class='fondo' style='margin-left:1em;' value='"._T('geoportail:info_supprimer')."' />"
					.fin_cadre_relief(true);
			}
			else
			{	$form .= debut_cadre_relief("", true)
					._T('geoportail:rgc_info_'.$rgc)
					."<input type='hidden' name='rgc_$count' value='$rgc' />"
					."<input type='submit' name='geoportail_$rgc' class='fondo' style='margin-left:1em;' value='"._T('geoportail:bouton_installer')."' />"
					.fin_cadre_relief(true);
				$count++;
			}
		}
		closedir($dir);

		$form .= fin_cadre_trait_couleur(true);		
		
		// Formulaire
		echo generer_action_auteur('geoportail_config',
			'geoportail_config',
			'./?exec=geoportail_config_rgc',
			$form,
			" method='post' name='formulaire'"
		);

		echo fin_gauche();
		echo fin_page();
	}
	else
	{	// Pas d'acces
		include_spip('inc/minipres');
		echo minipres();
	}

}

?>