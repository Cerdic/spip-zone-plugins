<?php

// exec/spiplistes_courriers_casier.php

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

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// _SPIPLISTES_EXEC_COURRIERS_LISTE

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_courrier');
include_spip('inc/plugin');
include_spip('inc/spiplistes_lister_courriers_listes');
include_spip('inc/spiplistes_api_abstract_sql');

function spiplistes_afficher_pile_messages() {

	$sql_select = "id_liste,titre,date,maj,periode,patron,statut";
	$list = sql_select($sql_select, 'spip_listes', "message_auto='oui' AND date > 0");

	if (sql_count($list) == 0) {
		return (false); 
	}
	
	$pile_result = ""
		. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_timer.gif', true, ''
			, _T('spiplistes:Messages_automatiques').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "casier_courriers"))
		. "\n"
		. "<table class='spiplistes-tab' width='100%'  border='0' cellspacing='1' cellpadding='0'>\n" 
		. "<tr>\n"
		. "<th>"._T('spiplistes:envoi_patron')."</td>\n"
		. "<th>"._T('spiplistes:sur_liste')."</td>\n"
		. "<th>"._T('spiplistes:prochain_envoi_prevu')."</td>\n"
		. "</tr>\n"
		;

	while($row = spip_fetch_array($list)) {
		foreach(explode(",", $sql_select) as $key) {
			$$key = $row[$key];
		}
	
		$proch = round((strtotime($date) - time()) / _SPIPLISTES_TIME_1_DAY);
		$date_dernier = date(_T('spiplistes:format_date'), strtotime($maj)) ;
		switch($statut) {
			case _SPIPLISTES_HEBDO_LIST:
			case _SPIPLISTES_WEEKLY_LIST:
				$periodicite = _T('spiplistes:Liste_hebdo');
				break;
			case _SPIPLISTES_MENSUEL_LIST:
			case _SPIPLISTES_MONTHLY_LIST:
				$periodicite = _T('spiplistes:Liste_mensuelle');
				break;
			case _SPIPLISTES_YEARLY_LIST:
				$periodicite = _T('spiplistes:Liste_annuelle');
				break;
			case _SPIPLISTES_DAILY_LIST:
				if($periode) {
					$periodicite = _T('spiplistes:Tous_les')." $periode "._T('spiplistes:jours');
				} else {
					$periodicite = _T('spiplistes:Listes_autre');
				}
				break;
			default:
				$periodicite = _T('spiplistes:envoi_manuel');
		}
	
		$pile_result .= ""
			. "<tr " . (($ii++ % 2) ? "class='row-even'" : "") . ">\n"
			. "<td><a href='" . generer_url_public('patron_switch',"patron=$patron&date=$date_dernier")."'>$patron</a>"
			. "<br />$periodicite</td>\n"
			. "<td><a href='" . generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste") . "'>$titre</a>"
			. "<br />".spiplistes_nb_abonnes_liste_str_get($id_liste)."."
			. "</td>"
			. "<td>"
			.	(
				($proch > 0)
				? _T('spiplistes:dans_jours')." <strong>$proch</strong> "._T('spiplistes:jours')."</td>\n"
				: "<strong>"._T('date_aujourdhui')."</strong></td>\n"
				)
			. "</tr>\n"
			;
	} // end while
	
	$pile_result .= ""
		. "</table>\n"
		. fin_cadre_enfonce(true)
		;
	return ($pile_result);
	
} // end spiplistes_afficher_pile_messages()


function exec_spiplistes_courriers_casier () {
	
	include_spip ('inc/acces');
	include_spip ('inc/filtres');
	include_spip ('inc/config');
	include_spip ('inc/barre');
	
	include_spip ('inc/mots');
	include_spip ('inc/documents');
	
	include_spip('inc/spiplistes_api_globales');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_naviguer_paniers');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $supp_dest
		;

	///////////////////////////
	// initialise les variables postées par formulaire (formulaire gerer)
	foreach(array(
		'btn_confirmer_envoi', 'id_courrier', 'id_liste'
		, 'statut'
		, 'btn_supprimer_courrier'
		, 'btn_arreter_envoi' // si valide, contient id du courrier
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier', 'id_liste'
		, 'btn_supprimer_courrier', 'btn_arreter_envoi'
		) as $key) {
		$$key = intval($$key);
	}
	
	$flag_modifiable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur));

	// confirmer l'envoi d'un courrier
	if($btn_confirmer_envoi 
		&& $flag_modifiable
	) {
		spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_STATUT_ENCOURS);
		if($id_liste > 0) {
			spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
			// passe le courrier directement a la meleuse
			spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste);
			spiplistes_log("SEND ID_COURRIER #$id_courrier ON ID_LISTE #$id_liste BY ID_AUTEUR #$connect_id_auteur");
		}
	}

	// supprimer un courrier des cases
	if($btn_supprimer_courrier
		&& $flag_modifiable
	) {
		sql_delete("spip_courriers", "id_courrier=".sql_quote($btn_supprimer_courrier)." LIMIT 1");
		spiplistes_courrier_supprimer_queue_envois('id_courrier', $btn_supprimer_courrier);
	}
	
	// arreter un courrier en cours d'envoi
	if(
		$btn_arreter_envoi 
		&& $flag_modifiable
	) {
		spiplistes_courrier_modifier(
			$btn_arreter_envoi 
			, array(
				'statut' => sql_quote(_SPIPLISTES_STATUT_STOPE)
				, 'date_fin_envoi' => "NOW()"
			)
		);
		spiplistes_courrier_supprimer_queue_envois('id_courrier', $btn_arreter_envoi);
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "courrier_casier";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));
	
	// la gestion des courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		. spiplistes_naviguer_paniers_courriers(_T('spiplistes:aller_au_panier_'), true)
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron(true)
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		;
			
	$icone = _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png';
	
	$_skip_statut = "Sauter une table pour afficher chronos";

	// Début de liste
	$listes_statuts = array(
		_SPIPLISTES_STATUT_ENCOURS, _SPIPLISTES_STATUT_REDAC, _SPIPLISTES_STATUT_READY
		, $_skip_statut
		, _SPIPLISTES_STATUT_AUTO, _SPIPLISTES_STATUT_PUBLIE
		, _SPIPLISTES_STATUT_VIDE, _SPIPLISTES_STATUT_IGNORE, _SPIPLISTES_STATUT_STOPE, _SPIPLISTES_STATUT_ERREUR
		);
	$mes_statuts = ($statut && in_array($statut, $listes_statuts)) ? array($statut) : $listes_statuts;
	foreach($mes_statuts as $statut) {

		if($statut == $_skip_statut) {
			// liste des courriers programmés (des listes)
			$page_result .= ""
				. spiplistes_afficher_pile_messages()
				. "<br />"
				;
		}
		else {
			$page_result .= ""
				. spiplistes_lister_courriers_listes(
					spiplistes_items_get_item("tab_t", $statut)
						.	(
							($desc = spiplistes_items_get_item("desc", $statut))
							? "<br /><span style='font-weight:normal;'>$desc</span>"
							: ""
							)
					, spiplistes_items_get_item("icon", $statut)
					, 'courriers'
					, $statut
					, false
					, 'position'
					, _SPIPLISTES_EXEC_COURRIER_GERER
				)
				;
		}
	}

	echo($page_result);
	
	// MODE HISTORIQUE FIN ---------------------------------------------------------
	
	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();

} // exec_spip_listes()

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
?>