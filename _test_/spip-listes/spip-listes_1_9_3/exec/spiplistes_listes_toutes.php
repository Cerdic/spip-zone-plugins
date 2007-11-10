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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_listes_toutes(){
	
	include_spip('inc/presentation');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_lister_courriers_listes');
	include_spip('inc/spiplistes_naviguer_paniers');
	
	global $connect_statut
		, $connect_id_auteur
		;

	$flag_editable = ($connect_statut == "0minirezo");

	if($flag_editable) {
		// initialise les variables postées par le formulaire
		foreach(array(
			'btn_supprimer_liste_confirme', 'id_liste' // _SPIPLISTES_EXEC_LISTE_GERER
			, 'btn_confirmer_envoi_maintenant', 'periode', 'auto_mois', 'titre_message'
			) as $key) {
			$$key = _request($key);
		}
		foreach(array('id_liste', 'periode') as $key) {
			$$key = intval($$key);
		}

		// envoyer maintenant demandé par _SPIPLISTES_EXEC_LISTE_GERER
		if($btn_confirmer_envoi_maintenant) {
			$sql_values .= "date=NOW(),periode=$periode,message_auto='oui',titre_message="._q($titre_message).",";
			if($auto_mois == 'oui') {
				$sql_values .= "statut='"._SPIPLISTES_MONTHLY_LIST."',";
			}
			$sql_values = rtrim($sql_values,",");
			$sql_query = "UPDATE spip_listes SET $sql_values WHERE id_liste=$id_liste LIMIT 1";
			$sql_result = spip_query($sql_query);
		}
		

		// suppression demandée par _SPIPLISTES_EXEC_LISTE_GERER
		if($btn_supprimer_liste_confirme && $id_liste) {
			$sql_query = "DELETE FROM spip_listes WHERE id_liste=$id_liste LIMIT 1";
			$sql_result = spip_query($sql_query);
			$sql_query = "DELETE FROM spip_auteurs_mod_listes WHERE id_liste=$id_liste";
			$sql_result = spip_query($sql_query);
			$sql_query = "DELETE FROM spip_auteurs_listes WHERE id_liste=$id_liste";
			$sql_result = spip_query($sql_query);
			spiplistes_log("ID_LISTE #$id_liste DELETED BY ID_AUTEUR #$connect_id_auteur");
		}
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	// la gestion des abonnés est réservée aux admins 
	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_naviguer_paniers_listes(_T('spiplistes:Aller_aux_listes'));
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	// MODE LISTES: afficher les listes --------------------------------------------
	
	$page_result = "";
	
	foreach(array(_SPIPLISTES_PRIVATE_LIST, _SPIPLISTES_PUBLIC_LIST, _SPIPLISTES_MONTHLY_LIST, _SPIPLISTES_TRASH_LIST) as $statut) {
		$page_result .= ""
			. spiplistes_lister_courriers_listes(
				spiplistes_items_get_item("tab_t", $statut)
					.	(
						($desc = spiplistes_items_get_item("desc", $statut))
						? "<br /><span style='font-weight:normal;'>$desc</span>"
						: ""
						)
				, spiplistes_items_get_item("icon", $statut)
				, 'listes'
				, $statut
				, false
				, 'position'
				, _SPIPLISTES_EXEC_LISTE_GERER
			)
			;
	}
	
	echo($page_result);
		
	// MODE EDIT LISTES FIN --------------------------------------------------------
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
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
