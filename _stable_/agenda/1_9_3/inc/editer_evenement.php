<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function Agenda_action_update_repetitions($id_evenement,$repetitions,$liste_mots){
	// evenement source
	$res = sql_select("*", "spip_evenements","id_evenement=".sql_quote($id_evenement));
	if ($row = sql_fetch(($res))){
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$horaire = $row['horaire'];
		$lieu = $row['lieu'];
		$date_debut = strtotime($row['date_debut']);
		$date_fin = strtotime($row['date_fin']);
		$duree = $date_fin - $date_debut;
		$id_evenement_source = $row['id_evenement_source'];
		$id_article = $row['id_article'];
		if ($id_evenement_source!=0)
			return; // pas un evenement source donc rien a faire ici

		$repetitions_updated = array();
		// mettre a jour toutes les repetitions deja existantes ou les supprimer si plus lieu
		$res = sql_select("id_evenement","spip_evenements","id_evenement_source=".sql_quote($id_evenement));
		while ($row = sql_fetch($res)){
			$date = strtotime(date('Y-m-d',$row['date_debut']));
			if (in_array($date,$repetitions)){
				// il est maintenu, on l'update
				$repetitions_updated[] = $date;
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);

				// TODO : prendre en charge la mise a jour uniquement si conforme a l'original
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;

				// mettre a jour l'evenement					
				Agenda_action_update_evenement(
					$row['id_evenement'],
					array(
						"titre" => $update_titre,
						"descriptif" => $update_descriptif,
						"lieu" => $update_lieu,
						"horaire" => $horaire,
						"date_debut" => $update_date_debut,
						"date_fin" => $update_date_fin,
						"id_article" => $id_article));
						
				Agenda_action_update_liste_mots($row['id_evenement'], $liste_mots);
			}
			else {
				// il est supprime
				sql_delete("spip_mots_evenements","id_evenement=".$row['id_evenement']);
				sql_delete("spip_evenements","id_evenement=".$row['id_evenement']);
			}

		}
		// regarder les repetitions a ajouter
		foreach($repetitions as $date){
			if (!in_array($date,$repetitions_updated)){
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;

				$id_evenement_new = sql_insert("spip_evenements",
					"(id_evenement_source,maj)",
					"(".sql_quote($id_evenement).",NOW())");
				if ($id_evenement_new==0)
					spip_log("agenda action formulaire article : impossible d'ajouter un evenement repete");
				else {
					// mettre a jour l'evenement
					Agenda_action_update_evenement(
						$id_evenement_new,
						array(
							"titre" => $update_titre,
							"descriptif" => $update_descriptif,
							"lieu" => $update_lieu,
							"horaire" => $horaire,
							"date_debut" => $update_date_debut,
							"date_fin" => $update_date_fin,
							"id_article" => $id_article));

					Agenda_action_update_liste_mots($id_evenement_new, $liste_mots);
				}
			}
		}
	}
}

function Agenda_action_supprime_repetitions($supp_evenement){
	$res = sql_select("*", "spip_evenements", "id_evenement_source=".sql_quote($supp_evenement));
	while ($row = sql_fetch($res)){
		$id_evenement = $row['id_evenement'];
		sql_delete("spip_mots_evenements", "id_evenement=".sql_quote($id_evenement));
		sql_delete("spip_evenements", "id_evenement=".sql_quote($id_evenement));
	}
}

function Agenda_action_update_liste_mots($id_evenement,$liste_mots){
	// suppression des mots obsoletes
	$cond_in = "";
	if (count($liste_mots))
		$cond_in = "AND " . sql_in('id_mot', $liste_mots, 'NOT');
	sql_delete("spip_mots_evenements", "id_evenement=".sql_quote($id_evenement)." ".$cond_in);
	// ajout/maj des nouveaux mots
	foreach($liste_mots as $id_mot){
		if (!sql_fetsel("*", "spip_mots_evenements", array("id_evenement=".sql_quote($id_evenement), "id_mot=".sql_quote($id_mot))))
			sql_insertq("spip_mots_evenements", array("id_mot"=>$id_mot, "id_evenement"=>$id_evenement));
	}
}

// modifier un evenement
function Agenda_action_update_evenement($id_evenement, $couples){
	// mettre a jour l'evenement
	return sql_updateq(
		"spip_evenements", 
		$couples, 
		"id_evenement = " . sql_quote($id_evenement));
}

// creer un nouvel evenement
function Agenda_action_insere_evenement($couples){
	// nouvel evenement
	$id_evenement = sql_insertq("spip_evenements", $couples);

	if (!$id_evenement){
		spip_log("agenda action formulaire article : impossible d'ajouter un evenement");
		return 0;
	} 
	return $id_evenement;	
}



function Agenda_action_supprime_evenement($id_article,$supp_evenement){
	$res = sql_select("*", "spip_evenements", array(
		"id_article=" . sql_quote($id_article),
		"id_evenement=" . sql_quote($supp_evenement)));
	if ($row = sql_fetch($res)){
		sql_delete("spip_mots_evenements", "id_evenement=".sql_quote($supp_evenement));
		sql_delete("spip_evenements", "id_evenement=".sql_quote($supp_evenement));
	}
	Agenda_action_supprime_repetitions($supp_evenement);
	$id_evenement = 0;
	return $id_evenement;
}

?>
