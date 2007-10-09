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
			'btn_supprimer_ok'
		)) as $key) {
		$$key = _request($key);
	}
	
	// la maintenance spiplistes est réservée à l'admin principal 
	$flag_autorise = ($connect_statut == "0minirezo") && ($connect_id_auteur == 1);
	
	$tous_les_statuts_courriers = array(_SPIPLISTES_STATUT_REDAC, _SPIPLISTES_STATUT_READY
			, _SPIPLISTES_STATUT_ENCOURS
			, _SPIPLISTES_STATUT_AUTO, _SPIPLISTES_STATUT_PUBLIE
			, _SPIPLISTES_STATUT_VIDE, _SPIPLISTES_STATUT_IGNORE, _SPIPLISTES_STATUT_STOPE, _SPIPLISTES_STATUT_ERREUR);

	$message_maintenance = array();
	
	/////////////////
	// Toujours faire ce qui est demandé
	if($flag_autorise && $btn_supprimer_ok) {
		foreach($tous_les_statuts_courriers as $statut) {
			if(_request("supprimer_$statut")) {
				if(spip_query("DELETE FROM spip_courriers WHERE statut='$statut'")) {
					$msg = "<span style='color:green;'>"._T('pass_ok')."</span>";
				}
				else {
					$msg = "<span style='font-weight:bold;color:red;'>"._T('pass_erreur')."</span>";
				}
				$message_maintenance[] = _T('spiplistes:Suppression_de')." : ".spiplistes_items_get_item('tab_t', $statut)."... ".$msg;
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

	echo "<br /><br /><br />\n";
	gros_titre(_T('titre_admin_tech'));

	debut_gauche();
	__plugin_boite_meta_info();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result = "";
	
	if(count($message_maintenance)) {
		$page_result .= "<ul style='padding-left:2ex;margin-bottom:2em;'>";
		foreach($message_maintenance as $texte) {
			$page_result .= "<li>$texte</li>\n";
		}
		$page_result .= "</ul>\n";
	}

	//////////////////////////////////////////////////////
	// Boite
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
					. "<input type='checkbox' name='supprimer_$statut' value='$statut' id='supprimer_$statut' />"
					. "<label for='supprimer_$statut'>".spiplistes_items_get_item('tab_t', $statut)."</label>\n"
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
		. "<label for='btn_supprimer_ok' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='btn_supprimer_ok' name='btn_supprimer_ok' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</p>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
} // exec_spiplistes_maintenance()
?>