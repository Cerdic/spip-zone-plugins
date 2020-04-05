<?php
/**
 * @version Original From SPIP-Listes-V :: Id: spiplistes_listes_forcer_abonnement.php paladin@quesaco.org
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

/**
 * Permet de forcer l'abonnement a une liste
 * $statut = "tous" => '6forum' + '1comite' + '0minirezo'
 * si statut == 'aucun', desabonne tous
 * @global string $connect_id_auteur
 * @global boolean $connect_toutes_rubriques
 * @global int $connect_id_auteur
 * @param int $id_liste
 * @param string $statut
 * @param boolean $forcer_format_reception
 * @return boolean
 */
function spiplistes_listes_forcer_abonnement ($id_liste, $statut, $forcer_format_reception) {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$id_liste = intval($id_liste);
	
	if ($id_liste <= 0) {
		return(false);
	}
	
	$sql_where = '';
	
	if($statut=="tous") {
		$sql_where = " (statut=".sql_quote('6forum')." OR statut=".sql_quote('1comite')." OR statut=".sql_quote('0minirezo').")";
	}
	if($statut=="auteurs") {
		$sql_where = " (statut=".sql_quote('1comite')." OR statut=".sql_quote('0minirezo').")";
	}
	else if(in_array($statut, array('6forum', '1comite', '0minirezo'))) {
		$sql_where = " statut=".sql_quote($statut)."";
	}
	
	if(!empty($sql_where)) {
		
		// cherche les non-abonnes
		/*
		 * "SELECT id_auteur FROM spip_auteurs WHERE $sql_where AND LENGTH(email) AND id_auteur NOT IN ($selection)"
		 */
		$selection =
			(spiplistes_spip_est_inferieur_193())
			? "SELECT id_auteur FROM spip_auteurs_listes WHERE id_liste=".sql_quote($id_liste)
			: sql_select("id_auteur", "spip_auteurs_listes", "id_liste=".sql_quote($id_liste),'','','','','',false)
			;
		$sql_result = sql_select(
			  'id_auteur'
			, 'spip_auteurs'
			, array(
				  $sql_where
				, "LENGTH(email)"
				, "id_auteur NOT IN ($selection)"
			)
		);

		if($sql_result) {

			$sql_values = $elargis = "";
			$nb = sql_count($sql_result);

			if($nb > 0) {
				while($row = sql_fetch($sql_result)) {
					$sql_values .= 
						" (".sql_quote(intval($row['id_auteur']))
						. ", $id_liste, NOW()"
						// rajoute le format si force'
						. (($forcer_format_reception) ? "," . sql_quote($forcer_format_reception) : "")
						. "),";
					$elargis .= sql_quote(intval($row['id_auteur']));
				}

				if(!empty($sql_values)) {
					$sql_values = rtrim($sql_values, ",");
					$sql_result = sql_insert('spip_auteurs_listes'
						, "(id_auteur, id_liste, date_inscription" . ($forcer_format_reception ? ",format" : "") . ")"
						, $sql_values
						);
					if($sql_result === false) {
						spiplistes_sqlerror_log("listes_forcer_abonnement");
						return(false);
					}
					else {
						spiplistes_log($nb . " AUTEURS ($statut) ADDED TO LISTE #$id_liste BY ID_AUTEUR #$connect_id_auteur");
						
						if($forcer_format_reception) {
							// le format est demande' force'.
							// rajouter les abonnes manquants a spip_auteurs_elargis
							$sql_insert = "
								INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`)
								SELECT l.id_auteur,l.format FROM spip_auteurs_listes AS l
									WHERE l.id_liste=" . sql_quote($id_liste) . " 
										AND NOT EXISTS (SELECT NULL FROM spip_auteurs_elargis AS e
											WHERE l.id_auteur = e.id_auteur)
								";
							if(sql_query($sql_insert) === false) {
								spiplistes_sqlerror_log("listes_forcer_abonnement");
							}
							else {
								spiplistes_log("RECEPT. FORMAT MODIFIED FOR ID_LISTE #$id_liste BY ID_AUTEUR #$connect_id_auteur");
							}
						}
						return($nb);
					}
				} 
			}
			return(0); // pas d'abo a rajouter. Pas une erreur.
		}
		return(false);
	}
	else if($statut == "aucun") {
	// desabonner tous

		$result = 0;
		$sql_result = sql_delete('spip_auteurs_listes', "id_liste=".sql_quote($id_liste));
		if($sql_result) {
			spiplistes_log("auteurs (tous) removed from id_liste #$id_liste by id_auteur #$connect_id_auteur");
			$result++;
			return($result);
		}
	}
	return(false);
} // end spiplistes_listes_forcer_abonnement()

