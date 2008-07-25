<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation_util');
include_spip('inc/vieilles_defs');
include_spip('balise/notation_balises');
include_spip('base/abstract_sql');

function formulaires_notation_charger_dist($type, $id_objet, $retour='', $row=array(), $hidden=''){

	// définition des valeurs de base du formulaire
	$valeurs = array(
		'type'=>$type,
		'id_objet'=>$id_objet,
		'editable'=>true
	);
	// on recupère l'id de l'auteur connecté sinon on le fixe à 0 et on récupère l'IP du visiteur
	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		$statut = $GLOBALS['auteur_session']['statut'];
	}else{
		$id_auteur = 0;
		$ip	= $_SERVER['REMOTE_ADDR'];
	}
	
	$acces = notation_get_acces();
	
	$id_table_objet = id_table_objet($type);
	
	// on récupère la note pondérée de l'objet et le nombre de votes
	$res = sql_select(
		"spip_notations_objets.$id_table_objet,spip_notations_objets.note_ponderee,spip_notations_objets.nombre_votes",
		"spip_notations_objets",
		"$id_table_objet=" . sql_quote($id_objet)
		);
	// on récupère la note de l'auteur pour l'élément en cours
	$note=0;
	$total=0;
	if ($row = sql_fetch($res)){
		$note = $row['note_ponderee'];
		$total = $row['nombre_votes'];
	}
	// et on les ajoute au contexte du formulaire html
	$valeurs["note"] = $note;
	$valeurs["total"] = $total;
	
	// l'auteur a-t-il déjà voté ?
	if($id_auteur){
		$deja_vote = sql_countsel(
			"spip_notations",
			"$id_table_objet=" . sql_quote($id_objet) . " AND id_auteur=" . sql_quote($id_auteur)
			);
	}
	// le visiteur a-t-il déjà voté ?
	if($ip){
		$deja_vote = sql_countsel(
			"spip_notations",
			"$id_table_objet=" . sql_quote($id_objet) . " AND ip=" . sql_quote($ip)
			);	
	}

	// peut il modifier son vote ?
	
	if($deja_vote)
		if(!lire_config('notation/change_note')){
			$valeurs['editable']=false;
			return $valeurs;
		}
	
	// est-on autorise a voter ?
	$isauteur = ($statut=="0minirezo" || $statut=="1comite");
	if ($acces=='all'){
		return $valeurs;
	}else{
		if (($acces=='ide' && $statut!='') || ($acces=='aut' && $isauteur) || ($acces=='adm' && $statut=="0minirezo")){
			return $valeurs;
		}else{
			$valeurs['editable']=false;
			return $valeurs;
		}
	}
}

function formulaires_notation_traiter_dist($type, $id_objet, $retour='', $row=array(), $hidden=''){
	return noter_objet_traiter($type, $id_objet, $retour, $row, $hidden);
}

function noter_objet_traiter($type, $id_objet){

	$id_table_objet = id_table_objet($type);
	
	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	}else{
		$id_auteur = 0;
	}

	$ip	= $_SERVER['REMOTE_ADDR'];
	
	//recuperation des champs
	$note = intval(_request("notation-$type$id_objet"));
	$robot = _request('content');
	$id_donnees	= _request('notation_id_donnees');
	$acces = notation_get_acces();

	$erreur = '';

	//  s'assurer que l'objet existe bien
	if (sql_countsel("spip_$type", "$id_table_objet=" . sql_quote($id_objet))){
		
		
		// On est en train de voter
		if (($id_donnees==$id_objet) && $robot==''){	// Note correcte ?
			if($note<1 || $note>notation_get_nb_notes()){
				$erreur = _T('notation:note_hors_plage');
			}else{
				include_spip('ecrire/inc_connect');
				// Si pas inscrit : recuperer la note de l'objet sur l'IP
				if ($id_auteur == 0){
					$res = sql_select(
						"spip_notations.id_notation,spip_notations.id_auteur,spip_notations.note",
						"spip_notations",
						"$id_table_objet=" . sql_quote($id_objet) . " AND ip=" . sql_quote($ip)
						);
				// Sinon rechercher la note de l'auteur
				}else{
					$res = sql_select(
						"spip_notations.id_notation,spip_notations.id_auteur,spip_notations.note",
						"spip_notations",
						"$id_table_objet=" . sql_quote($id_objet) . " AND id_auteur=" . sql_quote($id_auteur)
						);
				}
				// Premier vote
				if (sql_count($res) == 0){  // Remplir la table de notation
					sql_insertq("spip_notations", array(
						"objet" => $type,
						"$id_table_objet" => $id_objet,
						"id_auteur" => $id_auteur,
						"ip" => $ip,
						"note" => $note
						));
					$duchangement = true;
				}else{  // Modifier la note
					$row = sql_fetch($res);
					// Seulement si elle a changee ou que l'auteur a change
					if ($row['note'] != $note || ($row['id_auteur'] != $id_auteur)){  // Un auteur non reference ne remplace pas la note d'un auteur reference
						if ($row['id_auteur'] == 0 || $id_auteur != 0){
							sql_update("spip_notations", array(
								"note" => $note,
								"id_auteur" => $id_auteur),
								"id_notation=" . sql_quote($row["id_notation"])
								);
							$duchangement = true;
						}
					}
				}
				// Calculer la nouvelle note de l'article
				if ($duchangement){
					$res = sql_select(
						"spip_notations.$id_table_objet,spip_notations.note",
						"spip_notations",
						"$id_table_objet=" . sql_quote($id_objet)
						);
					$lanote = 0;
					$total = 0;
					while ($row =sql_fetch($res)){
						$lanote += $row['note'];
						$total++;
					}
					$lanote = $lanote/$total;
					$lanote = intval($lanote*100)/100;
					$note = round($lanote);
					$note_ponderee = notation_ponderee ($lanote, $total);
					// Mise à jour ou insertion ?
					if (!sql_countsel("spip_notations_objets", "$id_table_objet=" . sql_quote($id_objet))){
						// Remplir la table de notation des objets
						sql_insertq("spip_notations_objets", array(
							"objet" => $type,
							"$id_table_objet" => $id_objet,
							"note" => $note,
							"note_ponderee" => $note_ponderee,
							"nombre_votes" => $total
							));
					}else{
						// Mettre ajour dans les autres cas
						sql_update("spip_notations_objets", array(
							"note" => $lanote,
							"note_ponderee" => $note_ponderee,
							"nombre_votes" => $total),
							"$id_table_objet=" . sql_quote($id_objet)
							);
					}
				}
			}
		}
		$res = sql_select(
			"spip_notations_objets.$id_table_objet,spip_notations_objets.note_ponderee,spip_notations_objets.nombre_votes",
			"spip_notations_objets",
			"$id_table_objet=" . sql_quote($id_objet)
			);
		$lanote=0;
		$total=0;
		if ($row = sql_fetch($res)){
			$lanote = $row['note_ponderee'];
			$total = $row['nombre_votes'];
		}
		$note = round($lanote);
	}

	return array(
		true,
		"note"=>$note,
		"total"=>$total
	);

}


?>
