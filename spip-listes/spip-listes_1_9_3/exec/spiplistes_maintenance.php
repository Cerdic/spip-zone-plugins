<?php
/**
 * Script de maintenance proposant la maintenance :
 * - des courriers
 * - des listes
 * - des formats
 * - des abonnements
 * 
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api_courrier');

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
	// la globale de connect_id_auteur est string
	// c'est un entier qu'il faut envoyer a autoriser()
	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	// initialise les variables postees par le formulaire
	foreach(array_merge(
		array(
			'btn_supprimer_courriers', 'btn_reset_listes', 'btn_supprimer_listes'
			, 'btn_modifier_formats', 'confirmer_modifier_formats'
			, 'btn_supprimer_formats', 'confirmer_supprimer_formats'
			, 'btn_nettoyer_abos', 'confirmer_nettoyer_abos'
		)) as $key) {
		$$key = _request($key);
	}
	
	// la maintenance spiplistes est reservee a l'admin principal 
	$flag_autorise = autoriser('webmestre','','',$connect_id_auteur);
	
	$tous_les_statuts_courriers = array(_SPIPLISTES_COURRIER_STATUT_REDAC, _SPIPLISTES_COURRIER_STATUT_READY
			, _SPIPLISTES_COURRIER_STATUT_ENCOURS
			, _SPIPLISTES_COURRIER_STATUT_AUTO, _SPIPLISTES_COURRIER_STATUT_PUBLIE
			, _SPIPLISTES_COURRIER_STATUT_VIDE, _SPIPLISTES_COURRIER_STATUT_IGNORE
			, _SPIPLISTES_COURRIER_STATUT_STOPE, _SPIPLISTES_COURRIER_STATUT_ERREUR
			);

	$msg_maintenance = array();
	
	$sql_formats_where = spiplistes_formats_autorises('sql_where');

	/////////////////
	// Faire ce qui est demande par le formulaire
	if($flag_autorise) {
	
		$msg_ok = "<span style='color:green;'>"._T('pass_ok');
		$msg_bad = "<span style='font-weight:bold;color:red;'>"._T('pass_erreur');
		$msg_end = "</span>\n";
		
		// les courriers
		if($btn_supprimer_courriers) {
			foreach($tous_les_statuts_courriers as $statut) {
				if(_request("supprimer_courriers_$statut")) {
					if($statut == _SPIPLISTES_COURRIER_STATUT_ENCOURS) {
						// supprime d'abord de la queue d'envoi
						spiplistes_courrier_supprimer_queue_envois('statut', $statut);
						spiplistes_log("RESET spool ID_COURRIER #$id_courrier by ID_AUTEUR #$connect_id_auteur");
					}
					// supprime le courrier
					$msg = 
						(
							spiplistes_courrier_supprimer('statut', $statut)
						) 
						? $msg_ok 
						: $msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:suppression_de__s',
						array('s' => spiplistes_items_get_item('tab_t', $statut)."... : ".$msg.$msg_end)
						);
					spiplistes_log("DELETE courrier ID_COURRIER #$id_courrier by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les listes en chronos a repasser en non-chrono
		// en realite', conserve le statut mais supprime la date d'envoi
		// ainsi, la trieuse ne preparera pas le courrier
		if($btn_reset_listes) {
			foreach(spiplistes_listes_select("id_liste", "message_auto='oui'") as $row) {
				$id_liste = intval($row['id_liste']);
				$sql_table = "spip_listes";
				$sql_champs = array('message_auto' => 'non', 'date' => '');
				$sql_where = "id_liste=$id_liste";
				if(_request("reset_liste_$id_liste")) {
					$msg =
						(
						// reset liste 
							sql_updateq($sql_table, $sql_champs, $sql_where)
						)
						?	$msg_ok
						:	$msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:annulation_chrono_')." : ID_LISTE #$id_liste : ".$msg.$msg_end;
					spiplistes_log("RESET liste ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les listes (global)
		if($btn_supprimer_listes) {
			foreach(spiplistes_listes_select("id_liste,titre") as $row) {
				$titre = $row['titre'];
				$id_liste = intval($row['id_liste']);
				if(_request("supprimer_liste_$id_liste")) {
					$sql_where = "id_liste=".sql_quote($id_liste);
					$msg =
						spiplistes_listes_liste_supprimer($id_liste)
						?	$msg_ok
						:	$msg_bad
						;
					$msg_maintenance[] = _T('spiplistes:suppression_de_titre_result'
											, array(
													'titre' => $titre
													, 'result' => $msg.$msg_end
												   ));
					spiplistes_log("DELETE liste ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");
				}
			}
		}
		
		// les formats
		if($btn_modifier_formats || $btn_supprimer_formats) {
			
			$objet = array('objet' => _T('spiplistes:des_formats'));
			
			if($confirmer_modifier_formats && ($format = spiplistes_format_valide(_request('sl-le-format')))) {
				
				$msg =
					(
						spiplistes_format_abo_modifier('tous', $format)
					)
					?	$msg_ok
					:	$msg_bad
					;
				$msg_maintenance[] = _T('spiplistes:modification_objet', $objet)." : ".$msg.$msg_end;
				spiplistes_log("UPDATE ALL format $format by ID_AUTEUR #$connect_id_auteur");
				
			}
			if($confirmer_supprimer_formats) {
				$msg =
					(
						// vider la table des formats connus de spiplistes
						sql_delete("spip_auteurs_elargis", $sql_formats_where)
					)
					?	$msg_ok
					:	$msg_bad
					;
				$msg_maintenance[] = _T('spiplistes:suppression_', $objet)." : ".$msg.$msg_end;
				spiplistes_log("DELETE formats "._SPIPLISTES_FORMATS_ALLOWED." by ID_AUTEUR #$connect_id_auteur");
			}
		}
		
		/**
		 * Nettoyer les abonnements
		 */
		if($btn_nettoyer_abos && $confirmer_nettoyer_abos)
		{
			if($ids_auteurs = spiplistes_abonnements_zombies())
			{
				spiplistes_log ('CLEAN ABOS '.implode(',', $ids_auteurs));
				
				$msg =
					(spiplistes_abonnements_auteur_desabonner($ids_auteurs, 'toutes'))
					?	$msg_ok
					:	$msg_bad
					;
				$objet = array('objet' => _T('spiplistes:des_abonnements'));
				$msg_maintenance[] = _T('spiplistes:nettoyage_', $objet)." : ".$msg.$msg_end;
			}
		}

		// compter les listes
		$nb_listes = spiplistes_listes_compter();
		$nb_listes_desc = spiplistes_nb_listes_str_get ($nb_listes);
		$listes_array = spiplistes_listes_select("id_liste,statut,titre,message_auto");
		// listes auto (crhono) compte'es a part
		$nb_listes_auto = 0;
		foreach($listes_array as $row) {
			if($row['message_auto']=='oui') {
				$nb_listes_auto++;
			}
		}
		
		// compter les formats (les abonnes ayant de'fini un format)
		$nb_abonnes_formats = spiplistes_formats_compter ($sql_formats_where);
	
		$maintenance_url_action = generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE);
		
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('titre_admin_tech');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "maintenance";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . trim($titre_page), $rubrique, $sous_rubrique));

	if(!$flag_autorise) {
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
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
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
	// Boite de maintenance du casier a courriers
	$objet = array('objet' => _T('spiplistes:des_courriers'));
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		. spiplistes_form_debut ($maintenance_url_action, true)
		. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
		;
	if(spiplistes_courriers_statut_compter()) {
		$page_result .= spiplistes_form_fieldset_debut(_T('spiplistes:suppression_', $objet), true);
		foreach($tous_les_statuts_courriers as $statut) {
			if(spiplistes_courriers_statut_compter($statut)) {
				$titre = spiplistes_items_get_item('tab_t', $statut);
				$page_result .= spiplistes_form_input_checkbox ('supprimer_courriers_'.$statut, $statut, $titre, false, true);
			}
		}
		$page_result .= spiplistes_form_fieldset_fin(true);
	}
	else {
		$page_result .= spiplistes_form_message(_T('spiplistes:casier_vide'), true);
	}
	$page_result .= ""
		. spiplistes_form_bouton_valider ('btn_supprimer_courriers')
		. spiplistes_form_fin(true)
		. fin_cadre_trait_couleur(true)
		;

	/////////////////////////////////////////
	// boite de maintenance des listes : la date des listes sont remises a zero (supprimer les chronos)
	$objet = array('objet' => _T('spiplistes:des_listes'));
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		. debut_cadre_relief("", true, "", _T('spiplistes:supprimer_les_chronos'))
		;
	if($nb_listes_auto) {
		$page_result .= ""
			. spiplistes_form_debut ($maintenance_url_action, true)
			. "<p class='verdana2'>"._T('spiplistes:suppression_chronos_desc')."</p>\n"
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_chronos_', $objet)
					. spiplistes_fieldset_legend_detail(_T('spiplistes:total').": $nb_listes_auto / $nb_listes_desc")
				, true)
		;
		foreach($listes_array as $row) {
			if($row['message_auto']=='oui') {
				$titre = $row['titre'];
				$statut = "";
				$id_liste = intval($row['id_liste']);
				$page_result .= spiplistes_form_input_checkbox ('reset_liste_'.$id_liste, $id_liste, $statut.$titre, false, true);
			}
		}
		$page_result .= ""
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_reset_listes')
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
		. debut_cadre_relief("", true, "", _T('spiplistes:supprimer_les_listes'))
		;
	if($nb_listes) {
		$page_result .= ""
			. spiplistes_form_debut ($maintenance_url_action, true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_', $objet)
					. spiplistes_fieldset_legend_detail(_T('spiplistes:total').": $nb_listes_desc")
				, true)
			;
		foreach($listes_array as $row) {
			$id_liste = intval($row['id_liste']);
			$titre = $row['titre'];
			$statut = "<img src='".spiplistes_items_get_item("puce", $row['statut'])."' alt='".spiplistes_items_get_item("alt", $row['statut'])."' width='9' height='9' style='margin: 0 0.25ex' />";
			$page_result .= spiplistes_form_input_checkbox ('supprimer_liste_'.$id_liste, $id_liste, $statut.$titre, false, true);
		}
		$page_result .= ""
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider ('btn_supprimer_listes')
			. spiplistes_form_fin(true)
			;
	}
	else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_liste'), true);
	}
	$page_result .= ""
		. fin_cadre_relief(true)
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite maintenance des formats
	$objet = array('objet' => _T('spiplistes:des_formats'));
	$page_result .= ''
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:maintenance_objet', $objet))
		;
	if($nb_abonnes_formats > 0)
	{
		$nb_abonnes_formats_desc = 
						($nb_abonnes_formats == 1)
						? _T('spiplistes:total_1_abonne')
						: _T('spiplistes:total_n_abonnes', array('n' => $nb_abonnes_formats))
						;
		$page_result .= ""
			// forcer les formats de reception
			. spiplistes_form_debut ($maintenance_url_action, true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:forcer_formats_', $objet)
					. spiplistes_fieldset_legend_detail($nb_abonnes_formats_desc)
				, true) 
			. spiplistes_form_input_checkbox ('confirmer_modifier_formats', 'oui'
											  , _T('spiplistes:forcer_formats_desc'), false, true)
			. "<div id='sl-modif-fmt'>\n"
				. spiplistes_form_input_radio ($name = "sl-le-format", "html", _T('spiplistes:html'), true, true)
				. spiplistes_form_input_radio ($name, "texte", _T('spiplistes:texte'), false, true)
				. spiplistes_form_input_radio ($name, "non", _T('spiplistes:aucun'), false, true)
			. "</div>\n"
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_modifier_formats')
			. spiplistes_form_fin(true)
			
			. "<hr />\n"
			// supprimer les formats
			. spiplistes_form_debut ($maintenance_url_action, true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut (
				_T('spiplistes:suppression_', $objet)
					. spiplistes_fieldset_legend_detail($nb_abonnes_formats_desc)
				, true) 
			. spiplistes_form_input_checkbox ('confirmer_supprimer_formats', 'oui', _T('spiplistes:confirmer_supprimer_formats'), false, true)
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_supprimer_formats')
			. spiplistes_form_fin(true)
			;
	} else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_format'), true);
	}
	$page_result .= ""
		. fin_cadre_trait_couleur(true)
		;

	/**
	 * Boite maintenance des abonnements
	 * Détecte les id_auteur zombies dans la table
	 * des abonnements (spiplistes_auteurs_listes)
	 */
	$objet = array('objet' => _T('spiplistes:des_abonnements'));
	$page_result .= ""
		. debut_cadre_trait_couleur('administration-24.gif', TRUE, '',
									_T('spiplistes:maintenance_objet', $objet))
		;
	
	$ids_auteurs = spiplistes_abonnements_zombies ();
	
	if(($nb_abos = count($ids_auteurs)) > 0)
	{
		spiplistes_debug_log ('ZOMBIES '.implode(',', $ids_auteurs));
		
		$page_result .= ""
			. spiplistes_form_debut ($maintenance_url_action, true)
			. spiplistes_form_description(_T('spiplistes:conseil_sauvegarder_avant', $objet), true)
			. spiplistes_form_fieldset_debut(
								_T('spiplistes:nettoyage_', $objet)
								 . spiplistes_fieldset_legend_detail(_T('spiplistes:total')
											 . ': '
											 . trim(spiplistes_str_auteurs(count($ids_auteurs))))
							   , true)
			. spiplistes_form_input_checkbox ('confirmer_nettoyer_abos', 'oui'
											  , _T('spiplistes:confirmer_nettoyer_abos'), false, true)
			. spiplistes_form_fieldset_fin(true)
			. spiplistes_form_bouton_valider('btn_nettoyer_abos')
			. spiplistes_form_fin(true)
			;
	} else {
		$page_result .= spiplistes_form_message(_T('spiplistes:pas_de_pb_abonnements'), true);
	}
	
	$page_result .= fin_cadre_trait_couleur(true);
	
	// Fin de la page
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
	
} // exec_spiplistes_maintenance()

/******************************/

// CP-20080329: demande la liste des listes
// retourne un tableau des listes
function spiplistes_listes_select ($sql_select, $sql_where = "") {
	$result = array();
	if(!empty($sql_select) && ($r = sql_select($sql_select, "spip_listes", $sql_where))) {
		while($row = sql_fetch($r)) {
			$result[] = $row;
		}
	}
	return($result);
}

/**
 * @version CP-20080323
 * @param string $texte
 * @return string
 */
function spiplistes_fieldset_legend_detail ($texte = '') {
	$result = '';
	if(!empty($texte)) {
		$result = ' <span class="spiplistes-legend-stitre">('.$texte.')</span>'
			. PHP_EOL;
	}
	return ($result);
}

/**
 * Compter les abonnements qui n'ont plus d'abonnes
 * (soit l'abonné a un statut '5poubelle'
 * soit il n'est plus dans la table des auteurs)
 * @version CP-20110815
 * @todo a valider en SPIP 193
 * @return array id_auteur
 */
function spiplistes_abonnements_zombies () {

//La req SQL :
//
//SELECT id_auteur 
//FROM spip_auteurs_listes 
//WHERE id_auteur
//IN (SELECT id_auteur FROM spip_auteurs WHERE statut='5poubelle')
//OR id_auteur NOT IN (SELECT id_auteur FROM spip_auteurs)
//GROUP BY id_auteur
//ORDER BY id_auteur ASC

	$sql_select = 'id_auteur';
	$sql_from = 'spip_auteurs';
	$sql_where = 'statut='.sql_quote('5poubelle');
	$selection_poubelle = 
		(spiplistes_spip_est_inferieur_193())
		? 'SELECT '.$sql_select.' FROM '.$sql_from.' WHERE '.$sql_where
		: sql_select($sql_select, $sql_from, $sql_where,
					 '','','','','',false)
		;
	$sql_where = '';
	$selection_tous = 
		(spiplistes_spip_est_inferieur_193())
		? "SELECT $sql_select FROM $sql_from"
		: sql_select($sql_select, $sql_from, $sql_where,
					 '','','','','',false)
		;
	
	$sql_from = 'spip_auteurs_listes';
	$sql_result = sql_select(
		$sql_select,
		$sql_from,
		'id_auteur IN ('.$selection_poubelle.')
			OR id_auteur NOT IN ('.$selection_tous.')',
		'id_auteur',
		'id_auteur ASC'
		);
	if($sql_result === FALSE) {
		spiplistes_sqlerror_log("spiplistes_abonnements_zombies");
		return (FALSE);
	}
	$result = array();
	while($row = sql_fetch($sql_result)) {
		$result[] = $row['id_auteur'];
	}
	return($result);

}

