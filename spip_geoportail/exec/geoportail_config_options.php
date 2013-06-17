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
include_spip('public/geoportail_boucles');

function exec_geoportail_config_options()
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
			echo "<div class='lat inner'>"
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

		echo pipeline('affiche_droite',array('args'=>array('exec'=>'geoportail_config','what'=>'options'),'data'=>''));
		
		echo debut_droite('',true);
		
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
		$galbum = ($GLOBALS['meta']['geoportail_geoalbum'])?"CHECKED":"";
		
 		$geoportail_sysref = $GLOBALS['meta']['geoportail_sysref'];
 		$geoportail_provider = $GLOBALS['meta']['geoportail_provider'];
 		$geoportail_popup = $GLOBALS['meta']['geoportail_popup'];
		$ghover = ($GLOBALS['meta']['geoportail_hover'])?"CHECKED":"";

		if ($geoportail_popup=='spip') $geoportail_popup = 'SpipPopup';
		
		/* Type d'objet a georef */
		$form = debut_cadre_trait_couleur("administration-24.gif", true, "", _T('geoportail:options'))
			.debut_cadre_relief("base-24.gif",true,"", _T('geoportail:geoportail_defaut'))
				. _T('geoportail:geoprovider_info')."<br/>"
				."<p>"
				._T('geoportail:geoportail_provider')." : <select name='defaut_provider' class='fondl'>"
				."<option value='GEOP'".($geoportail_provider=='GEOP'?" selected":"").">G&eacute;oportail</option>"
				."<option value='OSM'".($geoportail_provider=='OSM'?" selected":"").">OpenStreetMap</option>"
				."<option value='GMAP'".($geoportail_provider=='GMAP'?" selected":"").">Google Maps</option>"
				."<option value='BING'".($geoportail_provider=='BING'?" selected":"").">Bing Maps</option>"
				."<option value='YHOO'".($geoportail_provider=='YHOO'?" selected":"").">Yahoo !</option>"
				."</select>"
				."<br/>"
				._T('geoportail:geoportail_zone')." : "
				.geoportail_popup_zone('zone', $gzone, 'fondl')
				."<input type='submit' name='provider' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
				."</p>"
			.fin_cadre_relief(true)
			.debut_cadre_relief("forum-public-24.gif",true,"", _T('geoportail:geoportail_popup'))
				. _T('geoportail:geopopup_info')
				."<p>"
				._T('geoportail:geopopup_forme')." <select name='popup' class='fondl'>"
				."<option value='Anchored'".($geoportail_popup=='Anchored'?" selected >":">")._T('geoportail:popup_anchored')."</option>"
				."<option value='FramedCloud'".($geoportail_popup=='FramedCloud'?" selected >":">")._T('geoportail:popup_framecloud')."</option>"
				."<option value='SpipPopup'".($geoportail_popup=='SpipPopup'?" selected >":">")._T('geoportail:popup_spip')."</option>"
				."<option value='SpipPopupjqBubble'".($geoportail_popup=='SpipPopupjqBubble'?" selected >":">")._T('geoportail:popup_jbubble')."</option>"
				."<option value='SpipPopupqTip'".($geoportail_popup=='SpipPopupqTip'?" selected >":">")._T('geoportail:popup_qtip')."</option>"
				."<option value='SpipPopupClassic'".($geoportail_popup=='SpipPopupClassic'?" selected >":">")._T('geoportail:popup_classic')."</option>"
				."<option value='SpipPopupShadow'".($geoportail_popup=='SpipPopupShadow'?" selected >":">")._T('geoportail:popup_ombre')."</option>"
				."<option value='SpipPopupThink'".($geoportail_popup=='SpipPopupThink'?" selected >":">")._T('geoportail:popup_pense')."</option>"
				."<option value='SpipPopupBlack'".($geoportail_popup=='SpipPopupBlack'?" selected >":">")._T('geoportail:popup_black')."</option>"
				."</select>"
				."</p><p>"
				."<input type='checkbox' name='hover' id='hover' $ghover><label for=hover>"._T('geoportail:geoportail_hover')."</label>"
				."<input type='submit' name='setpopup' class='fondo' style='margin-left:3em;' value='"._T('bouton_valider')."' />"
				."</p>"
			.fin_cadre_relief(true);
		/* Geoservices */
		if ($spip_version_branche<3)
		$form .= debut_cadre_relief("site-24.gif", true, "", _T('geoportail:geoservices'))
				. _T('geoportail:info_geoservice')
				."<p><input type='checkbox' name='service' id='service' $gservice><label for=service>"._T('geoportail:geoportail_services')."</label>"
				."<input type='submit' name='geoservice' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' /></p>"
			.fin_cadre_relief(true)
			.fin_cadre_trait_couleur(true);

		/* Type d'objet a georef */
		$form .= debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('geoportail:geoportail_objet'))
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

		if ($GLOBALS['spip_version_branche']>2) $form .= "<br/><input type='checkbox' name='docauto' id='docauto' $gdocauto style='margin-left:2em;'><label for=docauto>"._T('geoportail:info_documents_auto')."</label>";
		
		if (defined('_DIR_PLUGIN_ALBUMS')) $form .= "<br/><input type='checkbox' name='album' id='album' $galbum><label for=album>"._T('album:titre_album')."</label>";

		$form .= "<br/><input type='checkbox' name='rubrique' id='rubrique' $grubrique><label for=rubrique>"._T('spip:icone_rubriques')."</label>"
			."</td><td valign=top><input type='checkbox' name='mot' id='mot' $gmot><label for=mot>"._T('spip:icone_mots_cles')."</label>"
			."<br/><input type='checkbox' name='breve' id='breve' $gbreve><label for=breve>"._T('spip:icone_breves')."</label>"
			."<br/><input type='checkbox' name='syndic' id='syndic' $gsyndic><label for=syndic>"._T('spip:icone_sites_references')."</label>"
			."<br/><br/>"
			."<input type='submit' name='objet' class='fondo' style='margin-left:1em; float:right;' value='"._T('bouton_valider')."' />"
			."</td></tr></table>"

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