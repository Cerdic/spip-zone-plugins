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
* Affichage d'un geoservice
*
**/

include_spip('inc/compat_192');
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/documents');
include_spip('public/assembler');
include_spip('inc/geoportail_autorisations');

function exec_geoservice()
{	// Gestion 
	$id_rubrique = _request('id_rubrique');
	$id_geoservice = _request('id_geoservice');
	
	pipeline('exec_init',array('args'=>array('exec'=>'geoservice','id_geoservice'=>$id_geoservice),'data'=>''));

	// Recherche du service concerne
	if ($id_geoservice)
	{	$row = spip_fetch_array(spip_query("SELECT * FROM spip_geoservices WHERE id_geoservice='$id_geoservice'"));
	}

	// Affichage
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$page = _T('geoportail:geoservice');
	if ($row) $page .= " - ".$row['titre'];
	echo $commencer_page($page, "", "");
	if ($row)
	{	echo debut_gauche('', true);
			//gros_titre(_T('geoportail:geoservice'), "racine-site-24.gif");

		echo "<br/>";
		echo debut_boite_info(true);
			$res = "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"
				._T('geoportail:numero')
				." :<br /><span class='spip_xx-large'>"
				.$id_geoservice
				.'</span></div>'
				. icone_horizontale(_T("geoportail:voir_services"), generer_url_ecrire('geoservice_tous',"id_rubrique=$id_rubrique"), "racine-site-24.gif", "rien.gif","")
			;
			echo $res;
		echo fin_boite_info(true)."<br/>";

		// Logo de la rubrique
		if ($GLOBALS['spip_version_branche']>=3) 
		{	$GLOBALS['logo_libelles']['geoservice'] = _T('geoportail:logo_service');
			echo "<div class='lat'>"
				.recuperer_fond('prive/objets/editer/logo',array('objet'=>'geoservice','id_objet'=>$id_geoservice,'editable'=>autoriser('modifier', 'geoservice')))
				."</div>";
		}
		else
		{	$iconifier = charger_fonction('iconifier', 'inc');
			$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_service');
			if ($GLOBALS['spip_version_branche']>2) $b=false;
			else $b=autoriser('modifier', 'geoservice');
			echo $iconifier('id_geoservice', $id_geoservice, 'geoservice', $b);
		}
			
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'geoservice','id_geoservice'=>$id_geoservice),'data'=>''))
		.creer_colonne_droite('', true)
		.pipeline('affiche_droite',array('args'=>array('exec'=>'geoservice','id_geoservice'=>$id_geoservice),'data'=>''))
		.debut_droite('',true);

		
		echo debut_cadre_trait_couleur("site-24.gif", true);
			$rep = icone_inline(_T('geoportail:icone_modifier_service'), 
					generer_url_ecrire('geoservice_edit',"id_geoservice=$id_geoservice"), 
					"site-24.gif",
					"edit.gif",
					"right", 
					false);
			$rep .= icone_inline(_T('geoportail:icone_dupliquer_service'), 
					generer_url_ecrire('geoservice_edit',"id_copy=$id_geoservice"), 
					"breve-24.gif",
					"",
					"right", 
					false);
			echo $rep;
			
			if ($GLOBALS['spip_version_branche']>2) echo gros_titre (textebrut(typo($row['titre'])),puce_statut($row['statut']), false);
			else gros_titre (textebrut(typo($row['titre'])),'puce-'.puce_statut($row['statut']).'.gif');
			echo $row['type'];
			echo " - ".$row['zone'];
			$id_parent = $row['id_rubrique'];
			$rub = spip_fetch_array(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_parent'"));
			if ($rub) echo "<br/>"._T("geoportail:dans_rubrique")." : ".$rub['titre'];
			else echo "<br/>"._T("geoportail:toutes_rubriques");
			echo "<br/>"._T("geoportail:niveau")." : ".$row['niveau'];
			echo "<div align='$spip_lang_left' style='clear:both; padding: 5px; border: 1px dashed #aaaaaa; ' class='verdana1 spip_small'>"
				. propre($row['descriptif']."~"). "</div>\n";
			
			if (autoriser('publier','geoservice',$id_geoservice, NULL, $row))
			{	
				$statut = $row['statut'];
				$form = _T('geoportail:statut')." : <select class=fondl name=statut >"
					."<option value=prop ".($statut=='prop'?"SELECTED":"")." >"._T("geoportail:propose")."</option>"
					."<option value=publie ".($statut=='publie'?"SELECTED":"").">"._T("geoportail:publie")."</option>"
					."<option value=refuse ".($statut=='refuse'?"SELECTED":"").">"._T("geoportail:poubelle")."</option>"
					."</select>\n"
					."<input type='submit' id='valider_statut' name='valider_statut' class='fondo' value='"._T('spip:bouton_valider')."' style='margin:0 1em;' />";
				echo "<div style='padding:2em 3em 1em 1em; text-align:center;'>";
				echo generer_action_auteur('geoservice_edit',
					$id_geoservice,
					'./?exec=geoservice',	// BUG ? generer_url_ecrire('geoservice'),
					$form,
					" method='post' name='formulaire'"
				);
				echo "</div>\n";
			}

		echo fin_cadre_trait_couleur(true);
		
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'geoservice','id_geoservice'=>$id_geoservice),'data'=>''));
		
	}
	else echo "<strong>"._T('avis_acces_interdit')."</strong>";
		
	echo fin_gauche();
	echo fin_page();
}

?>