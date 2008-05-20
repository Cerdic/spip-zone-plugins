<?php
// Original From SPIP-Listes-V :: Id: spiplistes_listes_forcer_abonnement.php paladin@quesaco.org

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// permet de forcer l'abonnement à une liste
// $statut = "tous" => '6forum' + '1comite' + '0minirezo'
// si statut == 'aucun', désabonne tous

include_spip('inc/spiplistes_api_globales');

function spiplistes_listes_forcer_abonnement ($id_liste, $statut) {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$id_liste = intval($id_liste);
	
	if(!$id_liste) return(false);
	
	$sql_where = "";
	
	if($statut=="tous") {
		$sql_where = " (statut='6forum' OR statut='1comite' OR statut='0minirezo')";
	}
	if($statut=="auteurs") {
		$sql_where = " (statut=".sql_quote('1comite')." OR statut=".sql_quote('0minirezo').")";
	}
	else if(in_array($statut, array('6forum', '1comite', '0minirezo'))) {
		$sql_where = " statut='$statut'";
	}
	
	if(!empty($sql_where)) {
		
		// cherche les non-abonnés
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
		
spiplistes_log($nb = sql_count($sql_result)." AUTEURS ($statut) ADDED TO LISTE $id_liste BY ID_AUTEUR #$connect_id_auteur");

			$sql_values = "";

			if($nb > 0) {
				while($row = sql_fetch($sql_result)) {
					$sql_values .= " (".sql_quote(intval($row['id_auteur'])).", $id_liste, NOW()),";
				}
				if(!empty($sql_values)) {
						$sql_values = rtrim($sql_values, ",");
						return(
							sql_insert('spip_auteurs_listes'
							, "(id_auteur, id_liste, date_inscription)"
							, $sql_values
							)
						);
				} 
			}
			return(0); // pas d'abo à rajouter. Pas une erreur.
		}
		return(false);
	}
	else if($statut == "aucun") {
	// désabonner tous

		$result = 0;
		$sql_result = sql_delete('spip_auteurs_listes', "id_liste=".sql_quote($id_liste));
		if($sql_result) {
			spiplistes_log("AUTEURS (tous) REMOVED FROM LISTE $id_liste BY ID_AUTEUR #$connect_id_auteur");
			$result++;
			return($result);
		}
	}
	return(false);
} // spiplistes_listes_forcer_abonnement()


?>