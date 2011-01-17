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
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('geoportail:geoportail'), "", "");

	echo debut_gauche('',true);
	echo debut_droite('',true);
	echo gros_titre("<p>Plugin "._T('geoportail:geoportail')."</p>", '', false);
	
 	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;
 	
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques)
	{	$geoportail_key = $GLOBALS['meta']['geoportail_key'];
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

		/* Cle Geoportail */
		$form = debut_cadre_trait_couleur("redacteurs-24.gif", true, "", _T('geoportail:cle'))
			.debut_cadre_relief("",true)
			._T('geoportail:geoportail_key')
			.fin_cadre_relief(true)

			._T('geoportail:cle').' : '
			."&nbsp;<input type='text' name='geoportail_key' class='fondl' value=\"$geoportail_key\" size=30>"

			."<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"

			/* Geoservices */
			."<br/><input type='checkbox' name='service' id='service' $gservice><label for=service>"._T('geoportail:geoportail_services')."</label>"
	
			.debut_cadre_trait_couleur("warning-24.gif", true, "", "")
			._T('geoportail:geoportail_print')
			. fin_cadre_trait_couleur(true)
			
			.fin_cadre_trait_couleur(true);
		
		/* Type d'objet a georef */
		$form .= debut_cadre_trait_couleur("site-24.gif", true, "", _T('geoportail:geoportail_objet'))
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
		
		/* Systeme de reference */
		$form .= debut_cadre_trait_couleur("site-24.gif", true, "", _T('geoportail:geoportail_sysref'))
			.debut_cadre_relief("",true)
			. _T('geoportail:geoportail_sysinfo')
			.fin_cadre_relief(true);
		$code = explode(',',_T('geoportail:system_code'));
		$name = explode(',',_T('geoportail:system_name'));
		$form .= _T('geoportail:geoportail_sysref')." : <select name='syscode' class='fondl'>";
		for ($i=0; $i<sizeof($code); $i++) $form .= "<option value='".$code[$i].($code[$i]==$geoportail_sysref?"' selected>":"'>").$name[$i]."</option>";
		$form .= "</select>";
		$form .= "<input type='submit' name='sysref' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />"
			.fin_cadre_relief(true);

		/* RGC */
    // Pas de RGC installe
    $row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
    if (!$row) 
    {   effacer_meta('geoportail_rgc');
 				ecrire_metas();
        lire_metas();
    }

		$form .= debut_cadre_trait_couleur("ortho-24.gif", true, "", _T('geoportail:rgc'))
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
			'./?exec=geoportail_config',	// BUG ? generer_url_ecrire('geoportail_config'),
			$form,
			" method='post' name='formulaire'"
		);
	}
	else
	{	// Pas d'acces
		echo "<br/><br/>".gros_titre(_T('avis_non_acces_page'));
	}

	echo fin_gauche();
	echo fin_page();
}

?>