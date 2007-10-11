<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// _SPIPLISTES_EXEC_COURRIERS_LISTE

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/affichage');
include_spip ('base/spip-listes');
include_spip('inc/plugin');
include_spip('inc/spiplistes_lister_courriers_listes');

function spiplistes_afficher_pile_messages() {
	
	if ($GLOBALS['spip_version_code']<1.9204){
		include_spip('base/spiplistes_upgrade');
		if (!spiplistes_install('test'))
			spiplistes_install('install');
	}
	
	$sql_select = "id_liste,titre,date,maj,periode,patron,statut";
	$list = spip_query ("SELECT $sql_select FROM spip_listes WHERE message_auto='oui' AND date NOT LIKE "._q(_SPIPLISTES_ZERO_TIME_DATE));

	if (spip_num_rows($list) == 0) {
		return (false); 
	}
	
	$out = ""
		. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_timer.gif', true, ''
			, _T('spiplistes:Messages_automatiques').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "casier_courriers"))
		. "\n<style>
	table.tab td {
	text-align:center;
	padding:3px;
	width:33%;
	background-color:#ccc;
	}
	table.tab {
	margin-top:5px;
	}
	tr.row_even {
	background-color:#ccc;
	}
	</style>"
		. "<table class='tab'>" 
		. "<tr style='padding:5px'>"
		. "<td style='font-weight:bold;background-color:#eec'>"._T('spiplistes:envoi_patron')."</td>"
		. "<td style='font-weight:bold;background-color:#eec'>"._T('spiplistes:sur_liste')."</td>"
		. "<td style='font-weight:bold;background-color:#eec'>"._T('spiplistes:prochain_envoi_prevu')."</td>"
		. "</tr>"
		;

	while($row = spip_fetch_array($list)) {
		foreach(explode(",", $sql_select) as $key) {
			$$key = $row[$key];
		}
	
		$proch = round((strtotime($date) - time()) / _SPIPLISTES_TIME_1_DAY);
	
		if($i == 0){
			$out .= "<tr style='padding:5px'>" ;
			$i = 1 ;
		}
		else {
			$out .= "<tr style='padding:5px' class='row_even'>" ;
			$i = 0 ;
		} // end else
	
		$date_dernier = date(_T('spiplistes:format_date'), strtotime($maj)) ;

		$periodicite = 
			($statut = _SPIPLISTES_MONTHLY_LIST)
			? _T('spiplistes:Liste_mensuelle')
			: _T('spiplistes:Tous_les')." $periode "._T('spiplistes:jours')
			;

		$out .= ""
			. "<td><a href='" . generer_url_public('patron_switch',"patron=$patron&date=$date_dernier")."'>$patron</a>"
			. "<br />$periodicite</td>"
			. "<td><a href='" . generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste") . "'>$titre</a><br />"
			. "</td>"
			. "<td>"
			.	(
				($proch > 0)
				? _T('spiplistes:dans_jours')." <strong>$proch</strong> "._T('spiplistes:jours')."</td>"
				: "<strong>"._T('date_aujourdhui')."</strong></td>"
				)
			. "</tr>"
			;
	} // end while
	
	$out .= ""
		. "</table>"
		. fin_cadre_enfonce(true)
		;
	return ($out);
	
} // end spiplistes_afficher_pile_messages()


function exec_spiplistes_courriers_casier () {
	
	include_spip ('inc/acces');
	include_spip ('inc/filtres');
	include_spip ('inc/config');
	include_spip ('inc/barre');
	
	include_spip ('inc/mots');
	include_spip ('inc/documents');
	
	include_spip('inc/spiplistes_api');

	spiplistes_log("spiplistes_afficher_pile_messages() <<", LOG_DEBUG); 	

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $supp_dest
		;

	///////////////////////////
	// initialise les variables postées par formulaire
	foreach(array(
		'btn_confirmer_envoi', 'id_courrier', 'id_liste' // (formulaire gerer) confirmer envoi
		, 'statut'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier','id_liste') as $key) {
		$$key = intval($$key);
	}

	if($btn_confirmer_envoi 
		&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur))
		) {
		spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_ENCOURS."' WHERE id_courrier=$id_courrier LIMIT 1");
		if($id_liste > 0) {
			spiplistes_supprime_liste_envois($id_courrier);
			// passe le courrier à la méleuse
			spiplistes_remplir_liste_envois($id_courrier,$id_liste);
			spiplistes_log("SEND ID_COURRIER #$id_courrier ON ID_LISTE #$id_liste BY ID_AUTEUR #$connect_id_auteur");
		}
	}

	// à sécuriser ($connect_toutes_rubriques || $connect_id_auteur == id_auteur)
	if ($detruire_message = intval(_request('detruire_message'))) {
		spip_query("DELETE FROM spip_courriers WHERE id_courrier="._q($detruire_message));
		// A priori, 2 reliquats anciennes versions
		//spip_query("DELETE FROM spip_auteurs_messages WHERE id_message="._q($detruire_message));
		//spip_query("DELETE FROM spip_forum WHERE id_message="._q($detruire_message));
		// supprime de la queue d'envois
		spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$detruire_message");
	}
	
	// à sécuriser ($connect_toutes_rubriques || $connect_id_auteur == id_auteur)
	if ($btn_arreter_envoi = intval(_request('btn_arreter_envoi'))) {
		// demande arreter envoi du courrier encour
		spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_STOPE."' WHERE id_courrier=$btn_arreter_envoi LIMIT 1");
		// supprime de la queue d'envois
		spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$btn_arreter_envoi");
	}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	// la gestion des courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	// MODE HISTORIQUE: Historique des envois --------------------------------------
	
	$icone = _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png';
	
	$page_result = ""
		;	

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
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();

} // exec_spip_listes()

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>
