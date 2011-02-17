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
{	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	// Administrateur global seulement
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('geoportail:geoportail'), "", "");
		echo gros_titre("Plugin "._T('geoportail:geoportail'), '', false);
		
		echo debut_gauche('',true);
		
		// Logo de la rubrique
		$iconifier = charger_fonction('iconifier', 'inc');
		$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_spip');
		if ($GLOBALS['spip_version_code']>2) $b=false;
		else $b=autoriser('modifier', 'geoservice');
		echo debut_cadre_trait_couleur("", true)
			.$iconifier('id_geoservice', 0, 'geoservice', $b)
			._T("geoportail:logo_info")
			.fin_cadre_trait_couleur(true);
	
		echo creer_colonne_droite('', true);
		 
		$res = icone_horizontale(_T('geoportail:cles'), generer_url_ecrire("geoportail_config"), "racine-site-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:options'), generer_url_ecrire("geoportail_config_options"), "administration-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:rgc'), generer_url_ecrire("geoportail_config_rgc"), "breve-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);

		echo debut_droite('',true);
	 	
		$geoportail_key = $GLOBALS['meta']['geoportail_key'];
 		$yahoo_key = $GLOBALS['meta']['geoportail_yahoo_key'];

		/* Cle Geoportail */
		$form = debut_cadre_trait_couleur(_DIR_PLUGIN_GEOPORTAIL."img/geo.png", true, "", _T('geoportail:cle'))
			.debut_cadre_relief("",true)
			._T('geoportail:geoportail_key')
			.fin_cadre_relief(true)

			._T('geoportail:cle').' : '
			."&nbsp;<input type='text' name='geoportail_key' class='fondl' value=\"$geoportail_key\" size=30>"

			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"

			.debut_cadre_trait_couleur("warning-24.gif", true, "", "")
			._T('geoportail:geoportail_print')
			.fin_cadre_trait_couleur(true)
			. fin_cadre_trait_couleur(true);
			
		/* Cle Yahoo */
		$form .= debut_cadre_trait_couleur("groupe-mot-24.gif", true, "", _T('geoportail:cle_yahoo'))
			.'<p>'._T('geoportail:geoportail_yahoo_key').'</p>'

			._T('geoportail:cle').' : '
			."&nbsp;<input type='text' name='yahoo_key' class='fondl' value=\"$yahoo_key\" size=30>"

			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"

			.fin_cadre_trait_couleur(true);
		
		/* Cle Google (info) */
		$form .= debut_cadre_trait_couleur("fiche-perso-24.gif", true, "", _T('geoportail:cle_google'))
			.'<p>'._T('geoportail:geoportail_google_key').'</p>'
			.fin_cadre_trait_couleur(true);
		
		/* Layers OSM */
		$gtah = ($GLOBALS['meta']['geoportail_osm_tah'])?"CHECKED":"";
		$gmquest = ($GLOBALS['meta']['geoportail_osm_mquest'])?"CHECKED":"";
		$osmlayer = $GLOBALS['meta']['geoportail_osm_layer'];
		
		$form .= debut_cadre_trait_couleur("petition-24.gif", true, "", _T('geoportail:cle_osm'))
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
			.fin_cadre_trait_couleur(true);
		
		// Formulaire
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