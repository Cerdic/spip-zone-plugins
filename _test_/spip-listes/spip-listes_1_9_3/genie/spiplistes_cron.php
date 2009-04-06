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


	// Appele' en tache de fond (CRON SPIP)
	
	// Trieuse 
	
	// - Verifie toutes les listes auto==oui publiques et privees
	//   dont la date d'envoi est passee
	// - cree le courrier pour la meleuse dans la table spip_courriers
	// - determine les dates prochain envoi si periode > 0
	// - si periode < 0, repasse la liste en dormeuse

	// Precision sur la table spip_listes:
	// 'date': date d'envoi souhaitee (prochain envoi)
	// 'maj': date d'envoi du courrier mis a' jour par cron.
	// 'type' : type de liste attribuee soit en direct, via liste_gerer, 
	//          soit par la trieuse, en cron
	// type = 'nl' : (newsletter) liste envoyee en direct
	// type = 'auto' : liste traitee par la trieuse, en cron
	
/*
	cron_spiplistes_cron() renvoie:
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

	$prefix_log = "CRON: ";

	// initialise les options (prefs spiplistes)
	foreach(array(
		'opt_suspendre_trieuse'
		) as $key) {
		$$key = spiplistes_pref_lire($key);
	}

	if($opt_suspendre_trieuse == 'oui') {
		spiplistes_log($prefix_log."SUSPEND MODE !!!");
		if(spiplistes_courriers_en_queue_compter("etat=".sql_quote("")) > 0) {
			include_spip('inc/spiplistes_meleuse');
			return(spiplistes_meleuse($last_time));
		}
		else {
			return($last_time);
		}
	}

	$current_time = time();

	$sql_select = array(
		'id_liste', 'titre', 'titre_message', 'date', 'maj'
		, 'message_auto', 'periode', 'lang', 'patron', 'statut'
	);

	// demande les listes auto a' envoyer (date <= maintenant)
	$sql_where = "message_auto=".sql_quote('oui')."
			AND date > 0  
			AND date <= NOW()
			AND (".spiplistes_listes_sql_where_or(_SPIPLISTES_LISTES_STATUTS_OK).")
			"
		;
	$listes_privees_et_publiques = sql_select(
		$sql_select
		, 'spip_listes'
		, $sql_where
		);
	
	$nb_listes_ok = sql_count($listes_privees_et_publiques);
	
spiplistes_log($prefix_log."nb listes depart: ".$nb_listes_ok, _SPIPLISTES_LOG_DEBUG);

	if($nb_listes_ok > 0) {
	
		$mod_listes_ids = spiplistes_mod_listes_get_id_auteur("toutes");
		
		while($row = sql_fetch($listes_privees_et_publiques)) {
		
			// initalise les variables
			foreach($sql_select as $key) {
				$$key = $row[$key];
			}
			$id_liste = intval($id_liste);
			$periode = intval($periode);
			$envoyer_quand = $date;
			$dernier_envoi = $maj;
		
			// demande id_auteur de la liste pour signer le courrier
			// si plusieurs moderateurs, prend le premier
			$id_auteur = 
				(isset($mod_listes_ids[$id_liste]) && ($mod_listes_ids[$id_liste][0] > 0))
				? $mod_listes_ids[$id_liste][0]
				: 1 // attribue a l'admin principal si manquant
				;
			
			// Tampon date prochain envoi (dans 'date') et d'envoi (dans 'maj')
			$sql_set = $next_time = false;
			if(in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS_PERIODIQUES))) {
				$job_time = strtotime($envoyer_quand);
				$job_heure = date("H", $job_time);
				$job_minute = date("i", $job_time);
				$job_mois = date("m", $job_time);
				$job_jour = (($statut == _SPIPLISTES_MONTHLY_LIST) ? 1 : date("j", $job_time));
				$job_an = date("Y"); // la date est forcee par celle du systeme (eviter erreurs)
				switch($statut) {
					case _SPIPLISTES_YEARLY_LIST:
						$next_time = mktime($job_heure, $job_minute, 0, $job_mois, $job_jour, $job_an+1);
						break;
					case _SPIPLISTES_MENSUEL_LIST:
					case _SPIPLISTES_MONTHLY_LIST:
						$next_time = mktime($job_heure, $job_minute, 0, $job_mois+1, $job_jour, $job_an);
						break;
					case _SPIPLISTES_HEBDO_LIST:
					case _SPIPLISTES_WEEKLY_LIST:
						$next_time = mktime($job_heure, $job_minute, 0, $job_mois, $job_jour+7, $job_an);
						break;
					case _SPIPLISTES_DAILY_LIST:
						$next_time = mktime($job_heure, $job_minute, 0, $job_mois, $job_jour+$periode, $job_an);
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
				// pas de periode ? c'est un envoyer_maintenant.
				// applique le tampon date d'envoi et repasse la liste en auto non
				$sql_set = array('date' => sql_quote(''), 'message_auto' => sql_quote("non"));
			}
			if($next_time || count($sql_set)) {
				if($next_time) {
					// prochaine date d'envoi dans 'date'
					$sql_set = array('date' => sql_quote(normaliser_date($next_time)));
				}
				$sql_set['maj'] = 'NOW()';
				sql_update(
					'spip_listes'
					, $sql_set
					, "id_liste=".sql_quote($id_liste)." LIMIT 1"
					);
			}
	
			/////////////////////////////
			// preparation du courrier a placer dans le panier (spip_courriers)
			// en cas de periode, la date est dans le passe' pour avoir les elements publies depuis cette date
			$titre = ($titre_message =="") ? $titre._T('spiplistes:_de_').$GLOBALS['meta']['nom_site'] : $titre_message;

			list($courrier_html, $courrier_texte) = spiplistes_courriers_assembler_patron (
				_SPIPLISTES_PATRONS_DIR . $patron
				, array('date' => $dernier_envoi, 'patron'=>$patron, 'lang'=>$lang));
			
			$taille_courrier_ok = (($n = spiplistes_strlen(spiplistes_courrier_version_texte($courrier_html))) > 10);
spiplistes_log($prefix_log."taille courrier pour la liste $id_liste : $n", _SPIPLISTES_LOG_DEBUG);

			if($taille_courrier_ok) {
				include_spip('inc/filtres');
				$courrier_html = liens_absolus($courrier_html);
				$date_debut_envoi = $date_fin_envoi = "''";
				$statut = _SPIPLISTES_COURRIER_STATUT_ENCOURS;
			}
			else {
//spiplistes_log($prefix_log."courrier vide !!", _SPIPLISTES_LOG_DEBUG);
				$date_debut_envoi = $date_fin_envoi = "NOW()";
				$statut = _SPIPLISTES_COURRIER_STATUT_VIDE;
spiplistes_log($prefix_log."envoi mail nouveautes : courrier vide", _SPIPLISTES_LOG_DEBUG);
			}
			
			// Place le courrier dans le casier
			$id_courrier = sql_insert(
				'spip_courriers'
				,	"("
					. "titre
						,date
						,statut
						,type
						,id_auteur
						,id_liste
						,date_debut_envoi
						,date_fin_envoi
						,texte
						,message_texte"
					. ")"
				, 	"("
					. sql_quote($titre)
					. ",NOW()"
					. ",".sql_quote($statut)
					. ",".sql_quote(_SPIPLISTES_COURRIER_TYPE_LISTEAUTO)
					. ",".sql_quote($id_auteur)
					. ",".sql_quote($id_liste)
					. ",".$date_debut_envoi
					. ",".$date_fin_envoi
					. ",".sql_quote($courrier_html)
					. ",".sql_quote($courrier_texte)
					. ")"
			);

			if($taille_courrier_ok) {
				// place les etiquettes
				// (ajout des abonnes dans la queue (spip_auteurs_courriers))
				spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste);
			} 
		} // end while // fin traitement des listes
	}	
	
	/////////////////////////////
	// Si panier des etiquettes plein, appel de la meleuse
	if(
		$n = 
			spiplistes_courriers_en_queue_compter("etat=".sql_quote(""))
	){

spiplistes_log($prefix_log."$n job(s), appel meleuse", _SPIPLISTES_LOG_DEBUG);

		include_spip('inc/spiplistes_meleuse');
		return(spiplistes_meleuse($last_time));
	}
	else {
		spiplistes_log($prefix_log."NO JOB", _SPIPLISTES_LOG_DEBUG);
	}
	return ($last_time); 
} // cron_spiplistes_cron()

// En SPIP 192, c'est cron_* qui est appelle
// En SPIP 193, c'est genie_* qui est appelle

function genie_spiplistes_cron ($last_time) {
	include_spip('inc/spiplistes_api_globales');
// spiplistes_log("GENI: genie_spiplistes_cron() 193", _SPIPLISTES_LOG_DEBUG);
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