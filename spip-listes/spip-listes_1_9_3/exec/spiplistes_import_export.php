<?php
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_import_export(){

	include_spip('inc/presentation');
	include_spip('inc/acces');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;
	static $eol = PHP_EOL;
	
	// initialise les variables postées par le formulaire
	foreach(array(
		'btn_valider_import', 'abos_liste', 'format_abo', 'forcer_abo'	// retour import
		, 'btn_valider_export', 'export_id' // retour export
		, 'separateur', 'exporter_statut_auteur'
		) as $key) {
		$$key = _request($key);
	}

	$separateur = (($separateur == 'tab') ? "\t" : ';');
	
	$flag_admin = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	$flag_moderateur = false;
	
	$flag_autorise = 
		$flag_admin
		|| (
				$flag_moderateur = ($listes_moderees = spiplistes_mod_listes_id_auteur($connect_id_auteur))
			)
		;

	// exportation de liste. Retour formulaire local.
	// les admins tt rubriques peuvent tt exporter
	// le moderateur ne peut exporter que sa liste
	if(
		$flag_autorise
		&& 
			(	$flag_admin
				|| in_array($export_id, $listes_moderees)
			)
	) {
	
		// generation du fichier export ?
		if($btn_valider_export && $export_id) {
		
			$sql_select = array('a.email', 'a.nom', 'a.login', 'a.statut');
			$sql_from = array('spip_auteurs AS a');
			$sql_where = array("a.statut!=".sql_quote('5poubelle'));
			if(($id_liste = intval($export_id)) > 0) {
			// exportation d'une liste ID ? 
				$sql_from[] = "spip_auteurs_listes AS l";
				$sql_where[] = "l.id_liste=".sql_quote($id_liste);
				$sql_where[] = "a.id_auteur=l.id_auteur";
			}
			else {
			// autre type de liste
				if($export_id == "sans_abonnement") {
					if(spiplistes_spip_est_inferieur_193()) {
						$sql_where[] = "a.id_auteur NOT IN (SELECT id_auteur FROM spip_auteurs_listes GROUP BY id_auteur)";
					} else {
						$selection = sql_select("id_auteur", "spip_auteurs_listes", '','id_auteur','','','','',false);
						$sql_where[] = "a.id_auteur NOT IN ($selection)";
					}
				} 
				else if($export_id == "desabo") {
					$sql_from[] = "spip_auteurs_elargis AS f";
					$sql_where[] = "a.id_auteur=f.id_auteur";
					$sql_where[] = "f.`spip_listes_format`=".sql_quote('non');
				}
			}

			$sql_result = sql_select(
				$sql_select
				, $sql_from
				, $sql_where
				);

			$nb_inscrits = sql_count($sql_result);
			$exporter_statut_auteur = ($exporter_statut_auteur == 'oui');
			
			$str_export = ''
				. '# ' . spiplistes_html_signature(_SPIPLISTES_PREFIX, false).$eol
				. '# '._T('spiplistes:membres_liste').$eol
				. '# liste id: $export_id\n'
				. '# '.spiplistes_nom_site_texte().$eol
				. '# '.$GLOBALS['meta']['adresse_site'].$eol
				. '# date: '.date('Y-m-d').$eol
				. '# nb abos: '.$nb_inscrits.$eol.$eol
				. '#'.$eol
				. '# \'email\''.$separateur.'\'login\''.$separateur.'\'nom\''
				. ($exporter_statut_auteur ? $separateur.'\'statut\'' : '')
				. $eol.$eol
				;
			
			while($row = sql_fetch($sql_result)) {
				$str_export .= $row['email'].$separateur.$row['login'].$separateur.$row['nom']
					. ($exporter_statut_auteur ? $separateur.$row['statut'] : '')
					. "\n"
					;
			}
			// envoie le fichier
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="export_liste_$export_id-'.date("Y-m-d").'.txt"');
			echo ($str_export);
			exit;
		}
		// fin de generation du fichier export
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:listes_de_diffusion_');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "import_export";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . _T('spiplistes:import_export'), $rubrique, $sous_rubrique));

	// la gestion du courrier est réservée aux admins 
	if (!$flag_autorise) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}       

	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
	
	// importation
	$flag_import_fichier_ok = 
		(count($_FILES) && is_array($fichier_import = $_FILES['fichier_import']) 
		&& !$fichier_import['error']);

	if($btn_valider_import && $flag_import_fichier_ok) {
		if(!($abos_liste && is_array($abos_liste) && count($abos_liste))) {
		// A oublie' de selectionner une liste de destination
			$page_result .= spiplistes_boite_alerte(_T('spiplistes:Selectionnez_une_liste_pour_import'), true);
		}
	}
		
	// import form
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'listes_in-24.png', true
									, '', _T('spiplistes:Importer'))
		. "<p class='verdana2'>"._T('spiplistes:_aide_import')."</p>\n"
		;
	
	if($flag_import_fichier_ok)
	{
		//syslog(LOG_NOTICE, 'memory_limit: ' . get_cfg_var('memory_limit'));
		//syslog(LOG_NOTICE, 'memory_get_usage[1]: ' . memory_get_usage());
		//syslog(LOG_NOTICE, 'memory_get_peak_usage[1]: ' . memory_get_peak_usage());
		//syslog(LOG_NOTICE, 'filesize: ' . filesize($fichier_import['tmp_name']));
		   
		if($abos_liste && is_array($abos_liste) && count($abos_liste))
		{
			include_spip('inc/spiplistes_import');
			$page_result .= ''
				. debut_boite_info(true)
				. spiplistes_titre_boite_info(_T('spiplistes:Resultat_import'))
				. spiplistes_import(
					$fichier_import['tmp_name']
					, $fichier_import['name']
					, $abos_liste
					, $format_abo
					, $separateur
					, $flag_admin
					, $listes_moderees
					, $forcer_abo
					)
				. fin_boite_info(true)
				. '<br />'
				;
		}
	}

	$list = sql_select(
		array('id_liste','titre','texte')
		, 'spip_listes'
		, spiplistes_listes_sql_where_or(_SPIPLISTES_LISTES_STATUTS_OK));

	$nb_listes = sql_count($list);
	
	if($nb_listes) {
		$listes_array = array();
		while($row = sql_fetch($list)) {
			$listes_array[] = $row;
		}
	}
	
	if(!$nb_listes) {
		$page_result .= spiplistes_boite_alerte(_T('spiplistes:Pas_de_liste_pour_import'), true);
	} 
	else {
		$page_result .= ""
			. "<form action='" . generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT) . "' method='post' enctype='multipart/form-data'name='importform'>\n"
			. debut_cadre_relief("", true, "", _T('spiplistes:Liste_de_destination'))
			. "<p class='verdana2'>"._T('spiplistes:Selectionnez_une_liste_de_destination')."</p>\n"
			. "<ul class='liste-listes verdana2'>\n"
			;

		$listes_sans_patron = array();

		if(count($listes_array) > 0) {
			// une liste sans patron ne peut pas contenir d'abonnés.
			// récupère la liste des listes qui n'ont pas de patron.
			$sql_result = sql_select('id_liste', 'spip_listes'
				, array("patron=''"
					, "(statut=".implode(" OR statut=", array_map("sql_quote", explode(";", _SPIPLISTES_LISTES_STATUTS_OK))).")"
				)
			);
			while($row = sql_fetch($sql_result)) {
				$listes_sans_patron[] = $row['id_liste'];
			}
		}
		
		// liste des listes (destination)
		$couleur_ligne = 0;
		foreach($listes_array as $row) {
			$id_liste = $row['id_liste'] ;
			if(
				!in_array($id_liste, $listes_sans_patron)
				&&
				($flag_admin || in_array($id_liste, $listes_moderees))
			) {
				$titre = couper($row['titre'], 30, '...');
				$texte = couper($row['texte'], 30, '...');
				$label = _T('spiplistes:Liste_de_destination').": $titre";
				$checked = ($nb_listes == 1) ? "checked='checked'" : "";
				$class = ($couleur_ligne++ % 2) ? "class='row-even'" : "";
				$page_result .= ""
					. "<li style='padding:4px' $class >"
					. "<input name='abos_liste[]' type='checkbox' id='abos_$id_liste' value='$id_liste' title=\"$label\" $checked />\n"
					. "<label for='abos_$id_liste'><strong>".$titre."</strong> <em>".$texte."</em></label>\n"
					. "</li>\n"
					;
			}
		}
		$page_result .= ""
			. "</ul>"
			. fin_cadre_relief(true)
			//
			// Sélection du format de réception
			. debut_cadre_relief("", true, "", _T('spiplistes:format_de_reception_'))
			. "<ul class='liste-listes verdana2'>\n"
			. "<li>"
				. spiplistes_form_input_radio('format_abo', 'html', _T('spiplistes:html'), true, true, false)
				. "</li>\n"
			. "<li>"
				. spiplistes_form_input_radio('format_abo', 'texte', _T('spiplistes:texte'), false, true, false)
				. "</li>\n"
			. "<li>"
				. spiplistes_form_input_radio('format_abo', 'non', _T('spiplistes:desabonnement'), false, true, false)
				. "</li>\n"
			. "</ul>"
			. fin_cadre_relief(true)
			//
			// cadre insertion nom de fichier
			. debut_cadre_relief("", true, "", _T('spiplistes:importer_fichier'))
			. "<input type='file' size='40' name='fichier_import' />"
			//
			. spiplistes_fieldset_separateur($separateur)
			//
			// forcer les abonnements
			. spiplistes_fieldset_option(
				_T('spiplistes:option_import_')
				, 'forcer_abo'
				, 'oui'
				, _T('spiplistes:forcer_abos_'), ($forcer_abo == 'oui')
				
				)
			//
			. fin_cadre_relief(true)
			. spiplistes_form_bouton_valider('btn_valider_import')
			. spiplistes_form_fin(true)
			;
	} // end else
	$page_result .= fin_cadre_trait_couleur(true);
	// fin formulaire import

	// export //(original from erational.org)
	// formulaire d'export

	if ($nb_listes > 0) {
		$page_result .= ""
			. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'listes_out-24.png', true, "", _T('spiplistes:Exporter'))
			// exportation par listes
			. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT), true)
			. debut_cadre_relief("", true, "", _T('spiplistes:Exporter_une_liste_d_abonnes'))
			. "<ul class='liste-listes verdana2'>\n"
			;
		$couleur_ligne = 0;
		foreach($listes_array as $row) {
			$id_liste = intval($row['id_liste']);
			if($flag_admin || in_array($id_liste, $listes_moderees)) {
				$titre = couper($row['titre'], 30, '...');
				$class = ($couleur_ligne++ % 2) ? "class='row-even'" : "";
				list($nb_abos, $html, $texte) = spiplistes_listes_nb_abonnes_compter($id_liste, true);
				if($nb_abos > 0) {
					$page_result .= ""
						. "<li style='padding:4px'  $class >"
						. spiplistes_form_input_radio('export_id', $id_liste
							, "<strong>".$titre."</strong> <em>"
								. spiplistes_nb_abonnes_liste_str_get($id_liste, $nb_abos, $html, $texte)
								. "</em>"
							, ($nb_listes==1), true, false)
						. "</li>\n"
						;
				}
			}
		}
		$page_result .= ""
			. "</ul>"
			. fin_cadre_relief(true)
			. "<!-- fin de liste export -->\n"
			//
			// exportation autres
			. debut_cadre_relief("", true, "", _T('spiplistes:Exporter_une_liste_de_non_abonnes'))
			. "<div class='verdana2'>\n"
			. spiplistes_form_input_radio('export_id', 'sans_abonnement', _T('spiplistes:abonne_aucune_liste'), false, true, false)
			. spiplistes_form_input_radio('export_id', 'desabo', _T('spiplistes:desabonnes'), false, true, false)
			. "</div>"
			. fin_cadre_relief(true)
			. "<fieldset class='verdana2'><legend>"._T('spiplistes:export_etendu_').":</legend>"
			. spiplistes_form_input_checkbox('exporter_statut_auteur', 'oui'
				, _T('spiplistes:exporter_statut'), false, true, false)
			. "</fieldset>\n"
			//
			. spiplistes_fieldset_separateur($separateur)
			//
			. spiplistes_form_bouton_valider('btn_valider_export')
			. spiplistes_form_fin(true)
			. fin_cadre_trait_couleur(true)
			;
	}

	echo($page_result);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
}

function spiplistes_fieldset_separateur ($sep)  {
	$checked = ($sep=="\t");
	return(""
		. "<fieldset class='verdana2'><legend>"._T('spiplistes:separateur_de_champ_').":</legend>"
		. spiplistes_form_input_radio('separateur', 'tab'
			, _T('spiplistes:separateur_tabulation'), $checked, true, false)
		. spiplistes_form_input_radio('separateur', 'sc'
			, _T('spiplistes:separateur_semicolon'), !$checked, true, false)
		. "</fieldset>\n"
		);
}

function spiplistes_fieldset_option ($legend, $name, $value, $label, $checked = false)  {
	
	$result = "<fieldset class='verdana2'>"
		. "<legend>" . $legend . " : </legend>"
		. spiplistes_form_input_checkbox ($name, $value, $label, $checked, true, false)
		. "</fieldset>\n"
		;
	
	return($result);
}
?>