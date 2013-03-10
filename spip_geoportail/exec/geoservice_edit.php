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
* Forumlaire d'edition d'un geoservice
*
**/

include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/geoportail_autorisations');
include_spip('inc/geoportail_fonctions');
include_spip('inc/compat_192');

function exec_geoservice_edit()
{	// Gestion 
	$id_geoservice = _request('id_geoservice');

	pipeline('exec_init',array('args'=>array('exec'=>'geoservice_edit','id_geoservice'=>$id_geoservice),'data'=>''));

	// Recherche du service concerne
	if ($id_geoservice)
	{	$row = spip_fetch_array(spip_query("SELECT * FROM spip_geoservices WHERE id_geoservice='$id_geoservice'"));
	}
	else
	{	$row = array(
			'id_rubrique' => 0,
			'url_geoservice' => 'http://',
			'minzoom' => '5',
			'maxzoom' => '15',
			'opacity' => '1',
			'visibility' => '0',
			'selection' => '0',
			'maxextent' => '-180,-90,180,90'
		);
	}

	// Affichage
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$page = _T('geoportail:geoservice');
	if ($row) $page .= " - ".$row['titre'];
	echo $commencer_page($page, "", "");
	if ($row && autoriser('modifier', 'geoservice', $id_geoservice, NULL, $row))
	{	echo debut_gauche('', true);
			if ($id_geoservice)
			{	echo "<br/>";
				echo debut_boite_info(true);
					$res = "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"
						._T('geoportail:numero')
						." :<br /><span class='spip_xx-large'>"
						.$id_geoservice
						.'</span></div>'
						. icone_horizontale(_T("geoportail:voir_services"), generer_url_ecrire('geoservice_tous',""), "racine-site-24.gif", "rien.gif","")
					;
					echo $res;
				echo fin_boite_info(true)."<br/>";
			}

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'geoservice_edit','id_geoservice'=>$id_geoservice),'data'=>''))
		.creer_colonne_droite('', true)
		.pipeline('affiche_droite',array('args'=>array('exec'=>'geoservice_edit','id_geoservice'=>$id_geoservice),'data'=>''))
		.debut_droite('',true);

		// Formulaire de saisie
		echo debut_cadre_enfonce("", true);
		if ($id_geoservice) $url = generer_url_ecrire('geoservice',"id_geoservice=$id_geoservice");
		else $url = generer_url_ecrire('geoservice_tous');
		$rep = icone_inline(_T('icone_retour'), 
				$url, 
				"site-24.gif",
				"rien.gif",
				"left", 
				false);
		echo $rep . gros_titre (textebrut(typo($row['titre'])),'', false)
			. "<div style='display:block; clear:both; padding:0.5em 0;'><hr/>";
	
		// Gestion de la rubrique
		
		// Afficher le questionnaire
		$form .= recuperer_fond ('fonds/geoservices_edit',$row);
		$form .= "<p style='text-align:right'><input type='submit' id='valider' name='valider' class='fondo' value='"._T('spip:bouton_enregistrer')."' /></p>";
		
		// Formulaire
		echo generer_action_auteur('geoservice_edit',
			$id_geoservice ? $id_geoservice : 0,
			'./?exec=geoservice',	// BUG ? generer_url_ecrire(''),
			$form,
			" method='post' name='formulaire'"
		);
		echo "</div>".fin_cadre_enfonce(true);
		
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'geoservice_edit','id_geoservice'=>$id_geoservice),'data'=>''));
		
	}
	else echo "<strong>"._T('avis_acces_interdit')."</strong>";
		
	echo fin_gauche();
	echo fin_page();
}

?>