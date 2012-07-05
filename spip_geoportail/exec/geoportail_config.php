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

function exec_geoportail_config()
{	global $spip_version_branche, $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	// Administrateur global seulement
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('geoportail:geoportail'), "", "");
		echo gros_titre("Plugin "._T('geoportail:geoportail'), '', false);
		
		echo debut_gauche('',true);
		
		// Logo de la rubrique
		if ($spip_version_branche>=3) 
		{	$GLOBALS['logo_libelles']['geoservice'] = _T('geoportail:logo_spip');
			echo "<div class='lat'>"
				.recuperer_fond('prive/objets/editer/logo',array('objet'=>'geoservice','id_objet'=>0,'editable'=>autoriser('modifier', 'geoservice')))
				._T("geoportail:logo_info")
				."</div>";
		}
		else
		{	$iconifier = charger_fonction('iconifier', 'inc');
			$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_spip');
			if ($GLOBALS['spip_version_branche']>2) $b=false;
			else $b=autoriser('modifier', 'geoservice');
			echo debut_cadre_trait_couleur("", true)
				.$iconifier('id_geoservice', 0, 'geoservice', $b)
				._T("geoportail:logo_info")
				.fin_cadre_trait_couleur(true);
		}
	
		echo creer_colonne_droite('', true);
		 
		$res = icone_horizontale(_T('geoportail:cles'), generer_url_ecrire("geoportail_config"), ($spip_version_branche>=3?"racine-24.png":"racine-site-24.gif"),"rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:options'), generer_url_ecrire("geoportail_config_options"), ($spip_version_branche>=3?"configuration-24.png":"administration-24.gif"),"rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:rgc'), generer_url_ecrire("geoportail_config_rgc"), "breve-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:geoservices'), generer_url_ecrire("geoservice_tous"), "site-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);

		echo pipeline('affiche_droite',array('args'=>array('exec'=>'geoportail_config','what'=>'config'),'data'=>''));

		echo debut_droite('',true);
	 	
		/* Clé d'utilisation */
		$geoportail_key = $GLOBALS['meta']['geoportail_key'];
 		$yahoo_key = $GLOBALS['meta']['geoportail_yahoo_key'];
 		$bing_key = $GLOBALS['meta']['geoportail_bing_key'];

		/* Layers OSM */
		$gtah = ($GLOBALS['meta']['geoportail_osm_tah'])?"CHECKED":"";
		$gmquest = ($GLOBALS['meta']['geoportail_osm_mquest'])?"CHECKED":"";
		$osmlayer = $GLOBALS['meta']['geoportail_osm_layer'];

		/* Recherche si GeoportalExtended utilisateur */
		if (find_in_path ("js/GeoportalExtended.js"))
		{	$geoportail_js = "<input type='checkbox' name='js' id='js' ".($GLOBALS['meta']['geoportail_js'] ? "CHECKED":"")."><label for=js>"._T('geoportail:local_js')."</label>";
		}
		else if ($GLOBALS['meta']['geoportail_js'])
		{	effacer_meta ('geoportail_js');
			ecrire_metas();
		}
		
		$form = debut_cadre_trait_couleur("groupe-mot-24.gif", true, "", _T('geoportail:cles'))
			
			.debut_cadre_trait_couleur("",true)
			._T('geoportail:cle_info')
			.fin_cadre_couleur(true)

			/* Cle Geoportail */
			.debut_cadre_relief(_DIR_PLUGIN_GEOPORTAIL."img/geo.png",true)
			.'<p>'._T('geoportail:geoportail_key')
			."<br/>"
			._T('geoportail:cle_geoportail').' : '
			."&nbsp;<input type='text' name='geoportail_key' class='fondl' value=\"$geoportail_key\" size=30>"
			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
			."</p>"
			.fin_cadre_relief(true)

			/* Cle Bing */
			.debut_cadre_relief(_DIR_PLUGIN_GEOPORTAIL."img/powered_by_bing.png", true)
			.'<p>'._T('geoportail:geoportail_bing_key')
			."<br/>"
			._T('geoportail:cle_bing').' : '
			."&nbsp;<input type='text' name='bing_key' class='fondl' value=\"$bing_key\" size=30>"
			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
			."</p>"
			.fin_cadre_relief(true)
		
			/* Cle Yahoo */
			.debut_cadre_relief(_DIR_PLUGIN_GEOPORTAIL."img/powered_by_yahoo.png", true)
			.'<p>'._T('geoportail:geoportail_yahoo_key')
			."<br/>"
			._T('geoportail:cle_yahoo').' : '
			."&nbsp;<input type='text' name='yahoo_key' class='fondl' value=\"$yahoo_key\" size=30>"
			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
			."</p>"
			.fin_cadre_relief(true)

			/* Info Google */
			.debut_cadre_relief(_DIR_PLUGIN_GEOPORTAIL."img/powered_by_gmap.png", true)
			.'<p>'._T('geoportail:geoportail_google_key').'</p>'
			.fin_cadre_relief(true)

			/* Layers OSM */
			.debut_cadre_relief(_DIR_PLUGIN_GEOPORTAIL."img/powered_by_osm.png", true, "", " ")
			.'<p>'._T('geoportail:geoportail_osm_key').'</p>'
			.debut_cadre_relief("",true,"",_T('geoportail:osm_layers'))
			."<div style='width:12em; display:block; text-align:center'>"._T('geoportail:osm_affiche')."</div>"
			."<div style='padding:0 3em'>"
			."<input type='radio' style='width:2em' name='osmlayer' id='osmlayer' value='mapnik' ".($osmlayer=='mapnik'?"CHECKED":"").">"
			."<input type='checkbox' name='mapnik' id='mapnik' CHECKED disabled><label for=mapnik>"._T('geoportail:osm_osm')."</label>"
			."<br/>"
			."<input type='radio' style='width:2em' name='osmlayer' id='osmlayer' value='tah' ".($osmlayer=='tah'?"CHECKED":"").">"
			."<input type='checkbox' name='tah' id='tah' $gtah><label for=tah>"._T('geoportail:osm_tah')."</label>"
			."<br/>"
			."<input type='radio' style='width:2em' name='osmlayer' id='osmlayer' value='mquest' ".($osmlayer=='mquest'?"CHECKED":"").">"
			."<input type='checkbox' name='mquest' id='mquest' $gmquest><label for=mquest>"._T('geoportail:osm_mquest')."</label>"
			."<input type='submit' name='osmtile' class='fondo' style='margin-left:5em;' value='"._T('bouton_valider')."' />"
			."</div>"
			.fin_cadre_relief(true)
			.fin_cadre_relief(true)

			. fin_cadre_trait_couleur(true);
		
		/* Mode debug */
		$form .= debut_cadre_trait_couleur("administration-24.gif", true, "", _T('geoportail:geoportail_api'))
			. '<p>'._T('geoportail:geoportail_api_info').'</p>'
			. ($geoportail_js ? $geoportail_js : "")
			. fin_cadre_trait_couleur(true);	
					
		/* Formulaire */
		echo generer_action_auteur('geoportail_config',
			'geoportail_config',
			'./?exec=geoportail_config',
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