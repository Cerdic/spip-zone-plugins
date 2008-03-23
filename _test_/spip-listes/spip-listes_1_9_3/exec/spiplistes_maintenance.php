<?php

// exec/spiplistes_maintenance.php
// _SPIPLISTES_EXEC_MAINTENANCE

// From: paladin@quesaco.org
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_maintenance () {

	include_spip('inc/distant');
	include_spip('inc/meta');
	include_spip('inc/config');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $couleur_foncee
		, $spip_lang_right
		;
			
	// initialise les variables postées par le formulaire
	foreach(array_merge(
		array(
			'btn_supprimer_courriers', 'btn_reset_listes', 'btn_supprimer_listes'
			, 'btn_supprimer_formats', 'confirmer_supprimer_formats'
		)) as $key) {
		$$key = _request($key);
	}
	
	// la maintenance spiplistes est réservée à l'admin principal 
	$flag_autorise = ($connect_statut == "0minirezo") && ($connect_id_auteur == 1);
	
	$tous_les_statuts_courriers = array(_SPIPLISTES_STATUT_REDAC, _SPIPLISTES_STATUT_READY
			, _SPIPLISTES_STATUT_ENCOURS
			, _SPIPLISTES_STATUT_AUTO, _SPIPLISTES_STATUT_PUBLIE
			, _SPIPLISTES_STATUT_VIDE, _SPIPLISTES_STATUT_IGNORE, _SPIPLISTES_STATUT_STOPE, _SPIPLISTES_STATUT_ERREUR);

	$msg_maintenance = array();
	$sql_formats_where = ""
		. "`spip_listes_format`='"
		. implode("' OR `spip_listes_format`='", explode(";", _SPIPLISTES_FORMATS_ALLOWED))
		. "'";
	
	/////////////////
	// Faire ce qui est demandé par le formulaire
	if($flag_autorise) {
	
		$msg_ok = "<span style='color:green;'>"._T('pass_ok');
		$msg_bad = "<span style='font-weight:bold;color:red;'>"._T('pass_erreur');
		$msg_end = "</span>\n";
		
		// les courriers
		if($btn_supprimer_courriers) {
			foreach($tous_les_statuts_courriers as $statut) {
				if(_request("supprimer_courriers_$statut")) {
					if($statut == _SPIPLISTES_STATUT_ENCOURS) {
						// supprime de la queue d'envoi
						spip_query("DELETE FROM spip_auteurs_courriers
							WHERE id_courrier IN 
							(SELECT id_courrier FROM spip_courriers WHERE statut='$statut')
							");
						spiplistes_log("RESET spool ID_COURRIER #$id_courrier by ID_AUTEUR #$connect_id_auteur");
					}
					$msg = 
						(
							spip_query("DELETE FROM spip_courriers WHERE statut='$statut'")
						) 
						? $msg_ok 
						: $msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:Suppression_de')." : ".spiplistes_items_get_item('tab_t', $statut)."... : ".$msg.$msg_end;
					spiplistes_log("DELETE courrier ID_COURRIER #$id_courrier by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les listes en chronos
		if($btn_reset_listes) {
			foreach(spiplistes_listes_items_get("titre,id_liste") as $row) {
				$titre = $row['titre'];
				$id_liste = intval($row['id_liste']);
				if(_request("reset_liste_$id_liste")) {
					$msg =
						(
						// reset liste 
						spip_query("UPDATE spip_listes SET message_auto='non',date='' WHERE id_liste=$id_liste LIMIT 1")
						)
						?	$msg_ok
						:	$msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:annulation_chrono_')." : ".$titre."... : ".$msg.$msg_end;
					spiplistes_log("RESET liste ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les listes (global)
		if($btn_supprimer_listes) {
			foreach(spiplistes_listes_items_get("titre,id_liste") as $row) {
				$titre = $row['titre'];
				$id_liste = intval($row['id_liste']);
				if(_request("supprimer_liste_$id_liste")) {
					$msg =
						(
						// supprime la liste 
						spip_query("DELETE FROM spip_listes WHERE id_liste='$id_liste' LIMIT 1")
						// de la table des abonnés
						&& spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste='$id_liste'")
						// de la table des modérateurs (pas de LIMIT, si plusieurs modérateurs)
						&& spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste='$id_liste'")
						)
						?	$msg_ok
						:	$msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:Suppression_de')." : ".$titre."... : ".$msg.$msg_end;
					spiplistes_log("DELETE liste ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les formats
		if($btn_supprimer_formats && $confirmer_supprimer_formats) {
			$msg =
				(
				// vider la table des formats connus de spiplistes
				spip_query("DELETE FROM spip_auteurs_elargis WHERE $sql_formats_where")
				)
				?	$msg_ok
				:	$msg_bad
				;
			$objet = array('objet' => _T('spiplistes:des_formats'));
			$msg_maintenance[] = _T('spiplistes:suppression_', $objet)." : ".$msg.$msg_end;
			spiplistes_log("DELETE formats "._SPIPLISTES_FORMATS_ALLOWED." by ID_AUTEUR #$connect_id_auteur");
		}
	}
	
	// compter les listes
	$nb_listes = spiplistes_listes_count();
	$nb_listes_desc = 
						($nb_listes==1)
						? _T('spiplistes:info_1_liste')
						: "$nb_listes "._T('spiplistes:info_liste_2')
						;
	$listes_array = spiplistes_listes_items_get("id_liste,titre,message_auto");
	// listes auto (crhono) comptées à part
	$nb_listes_auto = 0;
	foreach($listes_array as $row) {
		if($row['message_auto']=='oui') {
			$nb_listes_auto++;
		}
	}
	
	// compter les formats (les abonnes ayant défini un format)
	$sql_query = "
		SELECT COUNT(id) as n 
		FROM spip_auteurs_elargis
		WHERE $sql_formats_where
		";
	$row = spip_fetch_array(spip_query($sql_query));
	$nb_abonnes_formats = $row['n'];
	$nb_abonnes_formats_desc = 
					($nb_abonnes_formats==1)
					? _T('spiplistes:info_1_abonne')
					: "$nb_abonnes_formats "._T('spiplistes:info_abonnes')
					;

	$maintenance_url_action = generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE);
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "maintenance";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	if(!$flag_autorise) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br />\n"
		. spiplistes_gros_titre(_T('titre_admin_tech'), '', true)
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_SPIPLISTES_PREFIX, true)
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron(true) 
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		;
	
	if(count($msg_maintenance)) {
		$page_result .= "<ul style='padding-left:2ex;margin-bottom:2em;'>";
		foreach($msg_maintenance as $texte) {
			$page_result .= "<li>$texte</li>\n";
		}
		$page_result .= "</ul>\n";
	}

	//////////////////////////////////////////////////////
	// Boite de maintenance du casier à courriers
	$objet = array('objet' => _T('spiplistes:des_courriers'));
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		. spiplistes_form_debut ($maintenance_url_action, 'post', true)
		. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
		;
	if(spiplistes_courriers_casier_count()) {
		$page_result .= spiplistes_form_fieldset_debut(_T('spiplistes:suppression_', $objet), true);
		foreach($tous_les_statuts_courriers as $statut) {
			if(spiplistes_courriers_casier_count($statut)) {
				$titre = spiplistes_items_get_item('tab_t', $statut);
				$page_result .= spiplistes_form_input_checkbox ('supprimer_courriers_'.$statut, $statut, $titre, false, true);
			}
		}
		$page_result .= spiplistes_form_fieldset_fin(true);
	}
	else {
		$page_result .= spiplistes_form_message(_T('spiplistes:Casier_vide'), true);
	}
	$page_result .= ""
		. spiplistes_form_bouton_valider ('btn_supprimer_courriers', _T('bouton_valider'), false, true)
		. spiplistes_form_fin(true)
		. fin_cadre_trait_couleur(true)
		;

	/////////////////////////////////////////
	// boite de maintenance des listes : date des listes remises à zéro (supprimer les chronos)
	$objet = array('objet' => _T('spiplistes:des_listes'));
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		. debut_cadre_relief("", true, "", _T('spiplistes:Supprimer_les_chronos'))
		;
	if($nb_listes_auto) {
		$page_result .= ""
			. spiplistes_form_debut ($maintenance_url_action, 'post', true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_', $objet).spiplistes_fieldset_legend_detail(_T('spiplistes:total').": $nb_listes_auto / $nb_listes_desc", true)
				, true)
		;
		foreach($listes_array as $row) {
			if($row['message_auto']=='oui') {
				$titre = $row['titre'];
				$id_liste = intval($row['id_liste']);
				$page_result .= spiplistes_form_input_checkbox ('reset_liste_'.$id_liste, $id_liste, $titre, false, true);
			}
		}
		$page_result .= ""
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_reset_listes', _T('bouton_valider'), false, true)
			. spiplistes_form_fin(true)
			;
	}
	else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_liste_en_auto'), true);
	}
	$page_result .= ""
		. fin_cadre_relief(true)
		;
		/////////////////////////////////////////
		// supprimer les listes
	$page_result .= ""
		. debut_cadre_relief("", true, "", _T('spiplistes:Supprimer_les_listes'))
		. spiplistes_form_debut ($maintenance_url_action, 'post', true)
		. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
		;
	if($nb_listes) {
		$page_result .= ""
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_', $objet).spiplistes_fieldset_legend_detail(_T('spiplistes:total').": $nb_listes_desc", true)
				, true)
			;
		foreach($listes_array as $row) {
			$titre = $row['titre'];
			$id_liste = intval($row['id_liste']);
			$page_result .= spiplistes_form_input_checkbox ('supprimer_liste_'.$id_liste, $id_liste, $titre, false, true);
		}
		$page_result .= spiplistes_form_fieldset_fin(true);
	}
	else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_liste'), true);
	}
	$page_result .= ""
		. spiplistes_form_bouton_valider ('btn_supprimer_listes', _T('bouton_valider'), false, true)
		. spiplistes_form_fin(true)
		. fin_cadre_relief(true)
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite maintenance des formats
	$objet = array('objet' => _T('spiplistes:des_formats'));
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		;
	if($nb_abonnes_formats > 0) {
		$page_result .= ""
			. spiplistes_form_debut ($maintenance_url_action, 'post', true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_', $objet).spiplistes_fieldset_legend_detail(_T('spiplistes:total').": $nb_abonnes_formats_desc", true)
				, true) 
			. spiplistes_form_input_checkbox ('confirmer_supprimer_formats', 'oui', _T('spiplistes:confirmer_supprimer_formats'), false, true)
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_supprimer_formats', _T('bouton_valider'), false, true)
			. spiplistes_form_fin(true)
			;
	} else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_format'), true);
	}
	$page_result .= ""
		. fin_cadre_trait_couleur(true)
		;
	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();
	
} // exec_spiplistes_maintenance()
?>