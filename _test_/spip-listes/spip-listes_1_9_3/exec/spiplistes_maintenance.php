<?php
// _SPIPLISTES_EXEC_MAINTENANCE

// From: paladin@quesaco.org
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_maintenance () {

	include_spip('inc/presentation');
	include_spip('inc/distant');
	include_spip('inc/affichage');
	include_spip('inc/meta');
	include_spip('inc/config');

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
	
	/////////////////
	// Toujours faire ce qui est demandé
	// les courriers
	if($flag_autorise) {
		$msg_ok = "<span style='color:green;'>"._T('pass_ok');
		$msg_bad = "<span style='font-weight:bold;color:red;'>"._T('pass_erreur');
		$msg_end = "</span>\n";
		if($btn_supprimer_courriers) {
			foreach($tous_les_statuts_courriers as $statut) {
				if(_request("supprimer_courriers_$statut")) {
					if($statut == _SPIPLISTES_STATUT_ENCOURS) {
						// supprime de la queue d'envoi
						spip_query("DELETE FROM spip_auteurs_courriers
							WHERE id_courrier IN 
							(SELECT id_courrier FROM spip_courriers WHERE statut='$statut')
							");
					}
					$msg = (spip_query("DELETE FROM spip_courriers WHERE statut='$statut'")) ? $msg_ok : $msg_bad;
					$msg_maintenance[] = _T('spiplistes:Suppression_de')." : ".spiplistes_items_get_item('tab_t', $statut)."... ".$msg.$msg_end;
				}
			}
		}
		// les listes
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
					$msg_maintenance[] = _T('spiplistes:Annulation_chrono_')." : ".$titre."... ".$msg.$msg_end;
				}
			}
		}
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
					$msg_maintenance[] = _T('spiplistes:Suppression_de')." : ".$titre."... ".$msg.$msg_end;
				}
			}
		}
	}
		
//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	if(!$flag_autorise) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	echo "<br /><br />\n";
	gros_titre(_T('titre_admin_tech'));

	debut_gauche();
	__plugin_boite_meta_info();
	spiplistes_boite_raccourcis();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result = "";
	
	if(count($msg_maintenance)) {
		$page_result .= "<ul style='padding-left:2ex;margin-bottom:2em;'>";
		foreach($msg_maintenance as $texte) {
			$page_result .= "<li>$texte</li>\n";
		}
		$page_result .= "</ul>\n";
	}

	//////////////////////////////////////////////////////
	// Boite du casier
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:Nettoyage_du_casier'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Conseil_sauvegarder_casier')."</p>\n"
		;
	if(spiplistes_courriers_casier_count()) {
		$page_result .= ""
			. "<fieldset class='verdana2'><legend>&nbsp;"._T('spiplistes:Supprimer_du_casier_les')."&nbsp;</legend>\n"
			;
		foreach($tous_les_statuts_courriers as $statut) {
			if(spiplistes_courriers_casier_count($statut)) {
				$page_result .= ""
					. "<div>"
					. "<input type='checkbox' name='supprimer_courriers_$statut' value='$statut' id='supprimer_courriers_$statut' />"
					. "<label for='supprimer_courriers_$statut'>".spiplistes_items_get_item('tab_t', $statut)."</label>\n"
					. "</div>\n"
				;
			}
		}
		$page_result .= ""
			. "</fieldset>"
			;
	}
	else {
		$page_result .= "<p class='verdana2'>"._T('spiplistes:Casier_vide')."</p>";
	}
	$page_result .= ""
		//
		// bouton valider casier
		. "<div class='verdana2' style='margin-top:1ex;text-align:$spip_lang_right;'>\n"
		. "<label for='btn_supprimer_courriers' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_supprimer_courriers' name='btn_supprimer_courriers' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite des listes
	$nb_listes = spiplistes_listes_count();
	$nb_listes_desc = 
						($nb_listes==1)
						? _T('spiplistes:info_1_liste')
						: "$nb_listes "._T('spiplistes:info_liste_2')
						;
	$listes_array = spiplistes_listes_items_get("id_liste,titre,message_auto");
	$nb_listes_auto = 0;
	foreach($listes_array as $row) {
		if($row['message_auto']=='oui') {
			$nb_listes_auto++;
		}
	}
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:Maintenance_des_listes'))
		// 
		/////////////////////////////////////////
		// Reset des listes : date des listes remises à zéro (supprimer les chronos)
		. debut_cadre_relief("", true, "", _T('spiplistes:Supprimer_les_chronos'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Conseil_sauvegarder_listes')."</p>\n"
		;
	if($nb_listes_auto) {
		$page_result .= ""
			. "<fieldset class='verdana2'><legend>&nbsp;"._T('spiplistes:Supprimer_les_chronos')
				. "&nbsp;<span class='spiplistes-legend-stitre'>("._T('spiplistes:Total').": $nb_listes_auto / $nb_listes_desc)</span></legend>\n"
			;
		foreach($listes_array as $row) {
			if($row['message_auto']=='oui') {
				$titre = $row['titre'];
				$id_liste = intval($row['id_liste']);
				$page_result .= ""
					. "<div>"
					. "<input type='checkbox' name='reset_liste_$id_liste' value='$id_liste' id='reset_liste_$id_liste' />"
					. "<label for='reset_liste_$id_liste'>$titre</label>\n"
					. "</div>\n"
					;
			}
		}
		$page_result .= ""
			. "</fieldset>"
			;
	}
	else {
		$page_result .= "<p class='verdana2'>"._T('spiplistes:Pas_de_liste_en_auto')."</p>";
	}
	$page_result .= ""
		// bouton valider les resets
		. "<div class='verdana2' style='margin-top:1ex;text-align:$spip_lang_right;'>\n"
		. "<label for='btn_reset_listes' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_reset_listes' name='btn_reset_listes' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. fin_cadre_relief(true)
		//
		/////////////////////////////////////////
		// supprimer les listes
		. debut_cadre_relief("", true, "", _T('spiplistes:Supprimer_les_listes'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Conseil_sauvegarder_listes')."</p>\n"
		;
	if($nb_listes) {
		$page_result .= ""
			. "<fieldset class='verdana2'><legend>&nbsp;"._T('spiplistes:Supprimer_la_liste')
				. "&nbsp;<span class='spiplistes-legend-stitre'>("._T('spiplistes:Total').": $nb_listes_desc)</span></legend>\n"
			;
		foreach($listes_array as $row) {
			$titre = $row['titre'];
			$id_liste = intval($row['id_liste']);
			$page_result .= ""
				. "<div>"
				. "<input type='checkbox' name='supprimer_liste_$id_liste' value='$id_liste' id='supprimer_liste_$id_liste' />"
				. "<label for='supprimer_liste_$id_liste'>$titre</label>\n"
				. "</div>\n"
				;
		}
		$page_result .= ""
			. "</fieldset>"
			;
	}
	else {
		$page_result .= "<p class='verdana2'>"._T('spiplistes:Pas_de_liste')."</p>";
	}
	$page_result .= ""
		// bouton valider le suppressions
		. "<div class='verdana2' style='margin-top:1ex;text-align:$spip_lang_right;'>\n"
		. "<label for='btn_supprimer_listes' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_supprimer_listes' name='btn_supprimer_listes' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. fin_cadre_relief(true)
		//
		// fin du cadre des listes
		. fin_cadre_trait_couleur(true)
		;

	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
} // exec_spiplistes_maintenance()
?>