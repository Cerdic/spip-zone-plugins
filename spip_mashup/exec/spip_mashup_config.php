<?php
/**
* Plugin SPIP-Mashup
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Configuration du plugin
*
**/

include_spip('inc/presentation');
include_spip('inc/config');

function exec_spip_mashup_config()
{	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	// Administrateur global seulement
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('spip_mashup:spip_mashup'), "", "");
		echo gros_titre("Plugin "._T('spip_mashup:spip_mashup'), '', false);
		
		echo debut_gauche('',true);
		
		echo "<img src='"._DIR_PLUGIN_SPIP_MASHUP."img/spip-mashup.png' style='width:100%'>";
	
		echo creer_colonne_droite('', true);
		 
		$res = icone_horizontale("Plugin "._T('geoportail:geoportail'), generer_url_ecrire("geoportail_config"), "racine-site-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);

		echo debut_droite('',true);
	 	
		/* Options du mashup */
		include_spip(mashup_fonctions);
		$options = mashup_getConfig();

		$backLayer = $options['backLayer']?"CHECKED":"";
		$zoom_pere = $options['zoom_pere']?"CHECKED":"";
		$zoom_img = $options['zoom_img']?"CHECKED":"";
		$no_popup = $options['no_popup']?"CHECKED":"";
		$popup = !$options['no_popup']?"CHECKED":"";

		$form = debut_cadre_trait_couleur("administration-24.gif", true, "", _T('geoportail:options'))

			// Toujours afficher les images en fond
			.debut_cadre_relief("",true)
				._T('spip_mashup:info1')."<br/>"
				."<input type='checkbox' style='margin-left:2em;' name='backLayer' id='backLayer' $backLayer><label for=backLayer> "._T('spip_mashup:backLayer')."</label><br/>"
			.fin_cadre_relief(true)
			
			.debut_cadre_relief("",true)
				._T('spip_mashup:info2')."<br/>"
				// Utilise le zoom du conteneur pour l'affichage des objets
				."<input type='checkbox' style='margin-left:2em;' name='zoom_pere' id='zoom_pere' $zoom_pere><label for=zoom_pere> "._T('spip_mashup:zoom_pere')."</label><br/>"
				// Utilise le zoom des images
				."<input type='checkbox' style='margin-left:2em;' name='zoom_img' id='zoom_img' $zoom_img><label for=zoom_img> "._T('spip_mashup:zoom_img')."</label><br/>"
			.fin_cadre_relief(true)

			.debut_cadre_relief("",true)
				._T('spip_mashup:info3')."<br/>"
				// Ne pas afficher de popup (acces direct)
				."<input type='radio' style='margin-left:2em;' value=0 name='no_popup' id='popup' $popup><label for=popup> ".($popup?"<b>":"")._T('spip_mashup:popup').($popup?"</b>":"")."</label>"
				."<input type='radio' style='margin-left:2em;' value=1 name='no_popup' id='no_popup' $no_popup><label for=no_popup> ".($no_popup?"<b>":"")._T('spip_mashup:no_popup').($no_popup?"</b>":"")."</label><br/>"
			.fin_cadre_relief(true)
			
			."<input type='submit' name='modifier' class='fondo' style='clear:both; float:right; margin:0 1em;' value='"._T('bouton_valider')."' />"
			.fin_cadre_trait_couleur(true)
			
			.debut_cadre_trait_couleur("vignette-24.png", true, "", _T('spip_mashup:param_img'))
			."<p>"._T('spip_mashup:info_img')."</p>"
			."<table style='margin:auto'><tr><td style='text-align:right' width=50%>"
			._T('spip_mashup:largeur')." : </td><td width=50%><input class='fondl'size=10 name='largeur' type='text' value='".$options['largeur']."' /> px"
			."</td></tr><tr><td style='text-align:right'>"
			._T('spip_mashup:largeur_mot')." : </td><td width=50%><input class='fondl'size=10 name='largeur_mot' type='text' value='".$options['largeur_mot']."' /> px"
			."</td></tr><tr><td style='text-align:right'>"
			._T('spip_mashup:couleur')." : </td><td><input class='palette fondl' size=10 name='couleur' type='text' value='".$options['bord_couleur']."' />"
			."</td></tr><tr><td style='text-align:right'>"
			._T('spip_mashup:bord')." : </td><td><input class='fondl'size=10 name='bord' type='text' value='".$options['bord']."' /> %"
			."</td></tr></table>"
			."<input type='submit' name='modifier' class='fondo' style='clear:both; float:right; margin:0 1em;' value='"._T('bouton_valider')."' />"
			.fin_cadre_trait_couleur(true);
			
		// Formulaire
		echo generer_action_auteur('spip_mashup_config',
			'spip_mashup_config',
			'./?exec=spip_mashup_config',
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