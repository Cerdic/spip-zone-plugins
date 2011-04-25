<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_listes_toutes(){
	
	include_spip('inc/presentation');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_lister_courriers_listes');
	include_spip('inc/spiplistes_naviguer_paniers');
	include_spip('inc/spiplistes_agenda');
	
	global $connect_statut
		, $connect_id_auteur
		;

	$flag_editable = ($connect_statut == "0minirezo");

	if($flag_editable) {
		// initialise les variables postees par le formulaire
		foreach(array(
			'btn_supprimer_liste_confirme', 'id_liste' // _SPIPLISTES_EXEC_LISTE_GERER
			, 'btn_confirmer_envoi_maintenant', 'titre_message'
			, 'periode_agenda' // local: pour afficher l'agenda
			) as $key) {
			$$key = _request($key);
		}
		foreach(array('id_liste', 'periode_agenda') as $key) {
			$$key = intval($$key);
		}

		// envoyer maintenant demande' par _SPIPLISTES_EXEC_LISTE_GERER
		if($btn_confirmer_envoi_maintenant && ($id_liste > 0)) {
			$array_set = array(
				'date' => 'NOW()'
			);
			if(!spiplistes_listes_liste_modifier($id_liste, $array_set)) {
				spiplistes_log("ERR: listes_modifier_liste #$id_liste");
			}
		}

		// suppression demandee par _SPIPLISTES_EXEC_LISTE_GERER
		if($btn_supprimer_liste_confirme 
			&& $id_liste
			&& spiplistes_listes_liste_supprimer($id_liste)
		) {
			spiplistes_log("ID_LISTE #$id_liste DELETED BY ID_AUTEUR #$connect_id_auteur");
		}
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:listes_de_diffusion_');
	// Permet entre autres d'ajouter les classes a la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "listes_toutes";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la gestion des abonnes est reservee aux admins 
	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_naviguer_paniers_listes(_T('spiplistes:aller_aux_listes_'), true)
		. spiplistes_boite_agenda($periode_agenda)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron() 
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
	
	// MODE LISTES: afficher les listes --------------------------------------------
	
	$page_result .= "";
	
	foreach(explode(";", _SPIPLISTES_LISTES_STATUTS_TOUS) as $statut) {
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
		
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
	
}

?>