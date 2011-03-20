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
		. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_timer.gif', true, ''
			, _T('spiplistes:Messages_automatiques').spiplistes_plugin_aide(_SPIPLISTES_EXEC_AIDE, "casier_courriers"))
		. "\n"
		. "<table class='spiplistes-tab' width='100%'  border='0' cellspacing='1' cellpadding='0'>\n" 
		. "<tr>\n"
		. "<th>"._T('spiplistes:envoi_patron')."</th>\n"
		. "<th>"._T('spiplistes:sur_liste')."</th>\n"
		. "<th>"._T('spiplistes:prochain_envoi_prevu')."</th>\n"
		. "</tr>\n"
		;

	$couleur_ligne = 1;
	while($row = sql_fetch($list)) {
		foreach(explode(",", $sql_select) as $key) {
			$$key = $row[$key];
		}
	
		$date_dernier = date(_T('spiplistes:format_date'), strtotime($maj)) ;
		switch($statut) {
			case _SPIPLISTES_LIST_PRIV_HEBDO:
			case _SPIPLISTES_LIST_PRIV_WEEKLY:
			case _SPIPLISTES_LIST_PUB_HEBDO:
			case _SPIPLISTES_LIST_PUB_WEEKLY:
				$periodicite = _T('spiplistes:Liste_hebdo');
				break;
			case _SPIPLISTES_LIST_PRIV_MENSUEL:
			case _SPIPLISTES_LIST_PRIV_MONTHLY:
			case _SPIPLISTES_LIST_PUB_MENSUEL:
			case _SPIPLISTES_LIST_PUB_MONTHLY:
				$periodicite = _T('spiplistes:Liste_mensuelle');
				break;
			case _SPIPLISTES_LIST_PRIV_YEARLY:
			case _SPIPLISTES_LIST_PUB_YEARLY:
				$periodicite = _T('spiplistes:Liste_annuelle');
				break;
			case _SPIPLISTES_LIST_PRIV_DAILY:
			case _SPIPLISTES_LIST_PUB_DAILY:
				if($periode) {
					$periodicite = _T('spiplistes:Tous_les_s'
					, array('s' => spiplistes_singulier_pluriel_str_get($periode, _T('spiplistes:jour'), _T('spiplistes:jours')))
					);
				} else {
					$periodicite = _T('spiplistes:Listes_autre');
				}
				break;
			default:
				$periodicite = _T('spiplistes:envoi_manuel');
		}
	
		$ii = 0;
		$pile_result .= ''
			. '<tr ' . ((($couleur_ligne++) % 2) ? 'class="row-even"' : '') . '>' . PHP_EOL
			. '<td><a href="' . generer_url_public('patron_switch','patron=$patron&date=$date_dernier').'">$patron</a>'
			. '<br />'.$periodicite.'</td>' . PHP_EOL
			. '<td><a href="' . generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, 'id_liste='.$id_liste) . '">'
				. $titre . '</a>'
			. '<br />'.spiplistes_nb_abonnes_liste_str_get($id_liste).'.'
			. '</td>'
			. '<td>'
			. spiplistes_affdate ($date)
			. '</td></tr>' . PHP_EOL
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
		'btn_confirmer_envoi', 'id_courrier', 'id_liste', 'id_auteur_test', 'btn_annuler_envoi'
		, 'statut'
		, 'btn_supprimer_courrier'
		, 'btn_arreter_envoi' // si valide, contient id du courrier
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier', 'id_liste', 'id_auteur_test'
		, 'btn_supprimer_courrier', 'btn_arreter_envoi'
		) as $key) {
		$$key = intval($$key);
	}

	$flag_admin = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	$flag_moderateur = count($listes_moderees = spiplistes_mod_listes_id_auteur($connect_id_auteur));
	$flag_createur = ($id_courrier && ($connect_id_auteur == spiplistes_courrier_id_auteur_get($id_courrier)));

	$flag_modifiable = ($flag_admin || $flag_moderateur || $flag_createur);

	if($flag_modifiable) {

		// annuler le destinataire d'un courrier (retour de courrier_gerer)
		// repasse le courrier en mode 'redac'
		if($btn_annuler_envoi) {
			spiplistes_courrier_modifier(
				$id_courrier
				, array(
					'email_test' => ''
					, 'id_liste' => 0
					, 'total_abonnes' => 0
					, 'statut' => _SPIPLISTES_COURRIER_STATUT_REDAC
				)						
			);
		}

		// confirmer l'envoi d'un courrier
		if($btn_confirmer_envoi) {
			// passe le courrier directement a la meleuse
			if($id_liste >= 0) {
				// destinataire(s) = abonnés à une liste
				// si id_liste == 0, destinataire = adresse email de test
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste, $id_auteur_test);
				if($id_liste > 0) {
					spiplistes_debug_log('SEND id_courrier #'.$id_courrier
										. ' ON id_liste #'.$id_liste
										. ' BY id_auteur #'.$connect_id_auteur
										);
				} else {
					spiplistes_debug_log('SEND id_courrier #'.$id_courrier
										 . ' TO id_auteur #'.$id_auteur_test
										 . ' TEST BY id_auteur #'.$connect_id_auteur
										 );
				}
			}
			spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_ENCOURS);
		}
	
		// supprimer un courrier des cases
		if($btn_supprimer_courrier) {
			sql_delete("spip_courriers", "id_courrier=".sql_quote($btn_supprimer_courrier)." LIMIT 1");
			spiplistes_courrier_supprimer_queue_envois('id_courrier', $btn_supprimer_courrier);
		}
		
		// arreter un courrier en cours d'envoi
		if($btn_arreter_envoi) {
			spiplistes_courrier_modifier(
				$btn_arreter_envoi 
				, array(
					'statut' => _SPIPLISTES_COURRIER_STATUT_STOPE
					, 'date_fin_envoi' => "NOW()"
				)
			);
			spiplistes_courrier_supprimer_queue_envois('id_courrier', $btn_arreter_envoi);
		}
		
	} // end if $flag_modifiable

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:casier_a_courriers');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "courriers_casier";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la gestion des courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_naviguer_paniers_courriers(_T('spiplistes:aller_au_panier_'), true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
			
	$icone = _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png';
	
	$_skip_statut = "Sauter une table pour afficher chronos";

	// Début de liste
	$listes_statuts = array(
		_SPIPLISTES_COURRIER_STATUT_ENCOURS, _SPIPLISTES_COURRIER_STATUT_REDAC
		, _SPIPLISTES_COURRIER_STATUT_READY
		, $_skip_statut
		, _SPIPLISTES_COURRIER_STATUT_AUTO, _SPIPLISTES_COURRIER_STATUT_PUBLIE
		, _SPIPLISTES_COURRIER_STATUT_VIDE, _SPIPLISTES_COURRIER_STATUT_IGNORE
		, _SPIPLISTES_COURRIER_STATUT_STOPE, _SPIPLISTES_COURRIER_STATUT_ERREUR
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
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();

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