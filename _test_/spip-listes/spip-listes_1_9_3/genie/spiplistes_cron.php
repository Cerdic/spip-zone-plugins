<?php

// genie/spiplistes_cron.php

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
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
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision: 15426 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-09-22 18:27:40 +0200 (sam., 22 sept. 2007) $

if (!defined("_ECRIRE_INC_VERSION")) return;


	// Appelé en tache de fond (CRON SPIP)
	
	// Trieuse 
	
	// - Verifie toutes les listes auto==oui publiques et privées
	//   dont la date d'envoi est passee
	// - crée le courrier pour la méleuse dans la table spip_courriers
	// - determine les dates prochain envoi si periode > 0
	// - si periode < 0, repasse la liste en dormeuse

	// Precision sur la table spip_listes:
	// 'date': date d'envoi souhaitee (prochain envoi)
	// 'maj': date d'envoi du courrier mis a' jour par cron.
	
/*
	renvoie:
	- nul, si la tache n'a pas a etre effectuee
	- positif, si la tache a ete effectuee
	- negatif, si la tache doit etre poursuivie ou recommencee

*/

function cron_spiplistes_cron ($last_time) { 

	include_spip('inc/utils');
	include_spip('inc/spiplistes_api_globales');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	include_spip('inc/spiplistes_api_abstract_sql');

	$current_time = time();

	// initialise les options
	foreach(array(
		'opt_suspendre_trieuse'
		) as $key) {
		$$key = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}

	$sql_select = "id_liste,titre,titre_message,date,maj,message_auto,periode,lang,patron,statut";

	// demande les listes auto a' envoyer (date <= maintenant)
	$sql_where = "message_auto='oui'
			AND (date > 0) 
			AND (date BETWEEN 0 AND NOW())
			AND (".spiplistes_listes_sql_where(_SPIPLISTES_LISTES_STATUTS_OK).")
			"
		;
	$listes_privees_et_publiques = sql_select(
		explode(',', $sql_select)
		, 'spip_listes'
		, $sql_where
		);
	
	$nb_listes_ok = sql_count($listes_privees_et_publiques);
	
spiplistes_log("CRON: nb listes ok: ".$nb_listes_ok, _SPIPLISTES_LOG_DEBUG);

	if($opt_suspendre_trieuse == 'oui') {
		spiplistes_log("TRI: SUSPEND MODE !!!");
		if($nb_listes_ok) {
			return(0 - $nb_listes_ok);
		}
		else {
			include_spip('inc/spiplistes_meleuse');
			return(spiplistes_meleuse());
		}
	}

	if($nb_listes_ok > 0) {
		while($row = sql_fetch($listes_privees_et_publiques)) {
		//while(false) {
		
			// initalise les variables
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			$id_liste = intval($id_liste);
			$periode = intval($periode);
			$envoyer_quand = $date;
			$dernier_envoi = $maj;
		
			// demande id_auteur de la liste pour la signer
			$id_auteur = spiplistes_mod_listes_get_id_auteur($id_liste);
			
			// Tampon date prochain envoi (dans 'date') et d'envoi (dans 'maj')
			$sql_set = $next_time = false;
			if(in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS_PERIODIQUES))) {
				$_time = strtotime($envoyer_quand);
				$_heure = date("H", $_time);
				$_minute = date("i", $_time);
				$_mois = date("m", $_time);
				$_jour = date("j", $_time);
				$_an = date("Y"); // la date est forcée par celle du système (eviter erreurs)
				switch($statut) {
					case _SPIPLISTES_YEARLY_LIST:
						$next_time = mktime($_heure, $_minute, 0, $_mois, $_jour, $_an+1);
						break;
					case _SPIPLISTES_MENSUEL_LIST:
					case _SPIPLISTES_MONTHLY_LIST:
						$next_time = mktime($_heure, $_minute, 0, $_mois+1, $_jour, $_an);
						break;
					case _SPIPLISTES_HEBDO_LIST:
					case _SPIPLISTES_WEEKLY_LIST:
						$next_time = mktime($_heure, $_minute, 0, $_mois, $_jour+7, $_an);
						break;
					case _SPIPLISTES_DAILY_LIST:
						$next_time = mktime($_heure, $_minute, 0, $_mois, $_jour+$periode, $_an);
						break;
					default:
						$sql_set = array('date' => sql_quote(''), 'message_auto' => sql_quote("non"));
						break;
				}
			}
			else if($periode) {
				$next_time = time() + (_SPIPLISTES_TIME_1_DAY * $periode);
			}
			else {
				// pas de période ? c'est un envoyer_maintenant.
				// Applique le tampon date d'envoi et repasse la liste en auto non
				$sql_set = array('date' => sql_quote(''), 'message_auto' => sql_quote("non"));
			}
			if($next_time || count($sql_set)) {
				if($next_time) {
					// prochaine date d'envoi dans 'date'
					$sql_set = array('date' => sql_quote(normaliser_date($next_time)));
				}
				$sql_set['maj'] = 'NOW()';
				sql_update('spip_listes'
					, $sql_set
					, "id_liste=".sql_quote($id_liste)." LIMIT 1"
					);
			}
	
	
			/////////////////////////////
			// preparation du courrier à placer dans le panier
			// en cas de période, la date est dans le passé pour avoir les elements publies depuis cette date
			include_spip('public/assembler');
			$contexte_patron = array('date' => $dernier_envoi, 'patron'=>$patron, 'lang'=>$lang);
			$texte = recuperer_fond('patrons/'.$patron, $contexte_patron);
			$titre = ($titre_message =="") ? $titre._T('spiplistes:_de_').$GLOBALS['meta']['nom_site'] : $titre_message;
			
	//spiplistes_log("CRON: Titre => $titre", _SPIPLISTES_LOG_DEBUG);
	
			$taille_courrier_ok = (spiplistes_strlen(spiplistes_courrier_version_texte($texte)) > 10);
	
			if($taille_courrier_ok) {
				include_spip('inc/filtres');
				$texte = liens_absolus($texte);
				$date_debut_envoi = $date_fin_envoi = "''";
				$statut = _SPIPLISTES_STATUT_ENCOURS;
			}
			else {
	//spiplistes_log("CRON: courrier vide !!", _SPIPLISTES_LOG_DEBUG);
				$date_debut_envoi = "date_debut_envoi=NOW()";
				$date_fin_envoi = "date_fin_envoi=NOW()";
				$statut = _SPIPLISTES_STATUT_VIDE;
			}
			
			/////////////////////////////
			// Place le courrier dans le casier
			sql_insert(
				'spip_courriers'
				, array(
					'titre' => sql_quote($titre)
					, 'date' => 'NOW()'
					, 'statut' => sql_quote($statut)
					, 'type' => sql_quote(_SPIPLISTES_TYPE_LISTEAUTO)
					, 'id_auteur' => sql_quote($id_auteur)
					, 'id_liste' => sql_quote($id_liste)
					, 'date_debut_envoi' => $date_debut_envoi
					, 'date_fin_envoi' => $date_fin_envoi
					, 'texte' => sql_quote($texte)
				)
			);
	
			/////////////////////////////
			// Ajoute les abonnés dans la queue (spip_auteurs_courriers)
			if($taille_courrier_ok) {
				$id_courrier = spip_insert_id();
				spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste);
			} 
			else {
				// contenu du courrier vide
	spiplistes_log("CRON: envoi mail nouveautes : courrier vide", _SPIPLISTES_LOG_DEBUG);
			} 
		}// end while // fin traitement des listes
	}	
	
	/////////////////////////////
	// Si panier courriers des encours plein, 
	// ou si queue en attente, appelle meleuse
	if (spiplistes_courriers_en_cours_count() 
		|| spiplistes_courriers_en_queue_count()){
		spiplistes_log("CRON: appel meleuse", _SPIPLISTES_LOG_DEBUG);
		include_spip('inc/spiplistes_meleuse');
		return(spiplistes_meleuse());
	}
	return ($last_time); 
} // cron_spiplistes_cron()

// SPIP 193 ?

function genie_spiplistes_cron ($last_time) {
	include_spip('inc/spiplistes_api_globales');
spiplistes_log("CRON: genie_spiplistes_cron() 193", _SPIPLISTES_LOG_DEBUG);
	cron_spiplistes_cron ($last_time);
}

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
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
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/

?>