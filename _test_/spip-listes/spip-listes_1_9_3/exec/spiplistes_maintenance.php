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
			'btn_supprimer_courriers', 'btn_supprimer_listes'
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
		// bouton valider
		. "<p class='verdana2' style='text-align:$spip_lang_right;'>\n"
		. "<label for='btn_supprimer_courriers' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_supprimer_courriers' name='btn_supprimer_courriers' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</p>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite des listes
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('spiplistes:Supprimer_les_listes'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Conseil_sauvegarder_listes')."</p>\n"
		;
	if($ii = spiplistes_listes_count()) {
		$page_result .= ""
			. "<fieldset class='verdana2'><legend>&nbsp;"._T('spiplistes:Supprimer_la_liste')
				."&nbsp;("
					.	(
						($ii==1)
						? _T('spiplistes:total_items', array('total'=>'', 'item'=>_T('spiplistes:info_1_liste')))
						: _T('spiplistes:total_items', array('total'=>$ii, 'item'=>_T('spiplistes:info_liste_2')))
						)
					.")</legend>\n"
			;
		foreach(spiplistes_listes_items_get("titre,id_liste") as $row) {
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
		//
		// bouton valider
		. "<p class='verdana2' style='text-align:$spip_lang_right;'>\n"
		. "<label for='btn_supprimer_listes' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_supprimer_listes' name='btn_supprimer_listes' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</p>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
} // exec_spiplistes_maintenance()
?>