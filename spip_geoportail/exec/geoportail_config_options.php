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

function exec_geoportail_config_options()
{	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;

	// Administrateur global seulement
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('geoportail:geoportail'), "", "");

		echo debut_gauche('',true);

		$res = icone_horizontale(_T('geoportail:cles'), generer_url_ecrire("geoportail_config"), "racine-site-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:options'), generer_url_ecrire("geoportail_config_options"), "administration-24.gif","rien.gif", false);
		$res .= icone_horizontale(_T('geoportail:rgc'), generer_url_ecrire("geoportail_config_rgc"), "breve-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);

		echo debut_droite('',true);
		echo gros_titre("<p>Plugin "._T('geoportail:geoportail')."</p>", '', false);
		
 		$garticle = ($GLOBALS['meta']['geoportail_geoarticle'])?"CHECKED":"";
 		$gauteur = ($GLOBALS['meta']['geoportail_geoauteur'])?"CHECKED":"";
 		$gdocument = ($GLOBALS['meta']['geoportail_geodocument'])?"CHECKED":"";
 		$gdocauto = ($GLOBALS['meta']['geoportail_geodocument_auto'])?"CHECKED":"";
 		$gdocauto .= ($GLOBALS['meta']['geoportail_geodocument'])?"":" DISABLED";
 		$grubrique = ($GLOBALS['meta']['geoportail_georubrique'])?"CHECKED":"";
 		$gmot = ($GLOBALS['meta']['geoportail_geomot'])?"CHECKED":"";
 		$gbreve = ($GLOBALS['meta']['geoportail_geobreve'])?"CHECKED":"";
 		$gsyndic = ($GLOBALS['meta']['geoportail_geosyndic'])?"CHECKED":"";
		$gservice = ($GLOBALS['meta']['geoportail_service'])?"CHECKED":"";
 		$geoportail_sysref = $GLOBALS['meta']['geoportail_sysref'];

		/* Type d'objet a georef */
		$form = debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('geoportail:geoportail_objet'))
			.debut_cadre_relief("",true)
			. _T('geoportail:geoobjet_info')
			.fin_cadre_relief(true)

			."<table cellspacing=10 style='padding:0 3em; width:100%'><tr><td>"
			."<input type='checkbox' name='article' id='article' $garticle><label for=article>"._T('spip:icone_articles')."</label>"
			."<br/><input type='checkbox' name='auteur' id='auteur' $gauteur><label for=auteur>"._T('spip:icone_auteurs')."</label>"
			."<br/><input type='checkbox' name='document' id='document' $gdocument "
			." onchange='javascript:jQuery(\"#docauto\").attr(\"disabled\",this.checked?\"\":\"disabled\")'><label for=document>"
			._T('spip:info_documents')."</label>"
			;

		if ($GLOBALS['spip_version_code']>2) $form .= "<br/><input type='checkbox' name='docauto' id='docauto' $gdocauto style='margin-left:2em;'><label for=docauto>"._T('geoportail:info_documents_auto')."</label>";
		
		$form .= "<br/><input type='checkbox' name='rubrique' id='rubrique' $grubrique><label for=rubrique>"._T('spip:icone_rubriques')."</label>"
			."</td><td valign=top><input type='checkbox' name='mot' id='mot' $gmot><label for=mot>"._T('spip:icone_mots_cles')."</label>"
			."<br/><input type='checkbox' name='breve' id='breve' $gbreve><label for=breve>"._T('spip:icone_breves')."</label>"
			."<br/><input type='checkbox' name='syndic' id='syndic' $gsyndic><label for=syndic>"._T('spip:icone_sites_references')."</label>"
			."<br/><br/>"
			."<input type='submit' name='objet' class='fondo' style='margin-left:1em; float:right;' value='"._T('bouton_valider')."' />"
			."</td></tr></table>"

			.fin_cadre_trait_couleur(true);

		
		/* Geoservices */
		$form .= debut_cadre_trait_couleur("site-24.gif", true, "", _T('geoportail:geoservices'))
			.debut_cadre_relief("",true)
			. _T('geoportail:info_geoservice')
			.fin_cadre_relief(true)
			."<p><input type='checkbox' name='service' id='service' $gservice><label for=service>"._T('geoportail:geoportail_services')."</label>"
			."<input type='submit' name='geoservice' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' /></p>"
			.fin_cadre_trait_couleur(true);
			
		/* Systeme de reference */
		$form .= debut_cadre_trait_couleur("administration-24.gif", true, "", _T('geoportail:geoportail_sysref'))
			.debut_cadre_relief("",true)
			. _T('geoportail:geoportail_sysinfo')
			.fin_cadre_relief(true);
		$code = explode(',',_T('geoportail:system_code'));
		$name = explode(',',_T('geoportail:system_name'));
		$form .= _T('geoportail:geoportail_sysref')." : <select name='syscode' class='fondl'>";
		for ($i=0; $i<sizeof($code); $i++) $form .= "<option value='".$code[$i].($code[$i]==$geoportail_sysref?"' selected>":"'>").$name[$i]."</option>";
		$form .= "</select>";
		$form .= "<input type='submit' name='sysref' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
			.fin_cadre_trait_couleur(true);

		// Formulaire
		echo generer_action_auteur('geoportail_config',
			'geoportail_config',
			'./?exec=geoportail_config_options',
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