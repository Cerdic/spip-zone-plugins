<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation');
include_spip('base/abstract_sql');
include_spip('inc/session');

/**
 * Charger la notation d'un objet
 * Attention : $objet est ici en fait le nom de la table
 * "articles" et pas "article", ce nommage est malheureux
 * 
 * @param <type> $objet
 * @param <type> $id_objet
 * @return boolean
 */
function formulaires_notation_charger_dist($objet, $id_objet){

	$type = objet_type($objet); // securite
	$table = table_objet($type);
	// definition des valeurs de base du formulaire
	$valeurs = array(
		'_objet'=>$table,
		'_id_objet'=>$id_objet,
		'editable'=>true,
		'_note_max' => notation_get_nb_notes(),
		'_form_id' => "-$table$id_objet"
	);

	// l'auteur ou l'ip a-t-il deja vote ?
	// si le visiteur a une session, on regarde s'il a deja vote
	// sinon, non (la verification serieuse en cas de vote deja effectue
	// se faisant dans verifier() )
	if ($GLOBALS['visiteur_session'] OR session_get('a_vote')) {

		// on recupere l'id de l'auteur connecte, sinon ip
		if (!$id_auteur = $GLOBALS['visiteur_session']['id_auteur']) {
			$id_auteur = 0;
			$ip	= $GLOBALS['ip'];
		}

		$where = array(
			"objet=" . sql_quote($type),
			"id_objet=" . sql_quote($id_objet),
			);
		if ($id_auteur)
			$where[] = "id_auteur=" . sql_quote($id_auteur);
		else
			$where[] = "ip=" . sql_quote($ip);
		$id_notation = sql_getfetsel("id_notation","spip_notations",$where);
		if ($id_notation){
			$valeurs['id_notation'] = $id_notation;
		}


	}
	// peut voter ou modifier son vote ?
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$type, 'id_objet'=>$id_objet))) {
		$valeurs['editable']=false;
	}

	return $valeurs;
}


function formulaires_notation_verifier_dist($objet, $id_objet){
	$erreurs = array();
	$type = objet_type($objet); // securite
	$table = table_objet($type);

	//  s'assurer que l'objet existe bien
	// et que le champ robot n'est pas rempli
	$trouver_table = charger_fonction('trouver_table','base');
	$table_objet = $trouver_table($table);
	$_id_objet = id_table_objet($table_objet['table']);

	if (!sql_countsel($table_objet['table'], "$_id_objet=" . sql_quote($id_objet))
		OR (_request('content')!="")) {
		$erreurs['message_erreur'] = ' ';
	// note dans la bonne fourchette
	} else {
		$note = intval(_request("notation-$objet$id_objet"));
		if(($note<1 || $note>notation_get_nb_notes())
			AND ($note!==-1) // annulation du vote
			)
			$erreurs['message_erreur'] = _T('notation:note_hors_plage'). $note;
	}
	return $erreurs;
}

function formulaires_notation_traiter_dist($objet, $id_objet){
	$type = objet_type($objet); // securite
	$table = table_objet($type);

	// indiquer dans sa session que ce visiteur a vote
	// grace a ce cookie on dira a charger() qu'il faut regarder
	// de plus pres ce qu'il en est dans la base

	session_set('a_vote', true);

	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	} else {
		$id_auteur = 0;
	}
	$ip	= $GLOBALS['ip'];

	// recuperation des champs
	$note = intval(_request("notation-$table$id_objet"));
	$id_donnees	= _request('notation_id_donnees'); // ne sert a rien ?

	// Si pas inscrit : recuperer la note de l'objet sur l'IP
	// Sinon rechercher la note de l'auteur
	$where = array(
		"objet=" . sql_quote($type),
		"id_objet=" . sql_quote($id_objet),
		);
	if ($id_auteur != 0) $where[] = "id_auteur=" . sql_quote($id_auteur);
	else $where[] = "ip=" . sql_quote($ip);
	$row = sql_fetsel(
		array("id_notation", "id_auteur", "note"),
		"spip_notations",
		$where
	);

	include_spip('action/editer_notation');
	// Premier vote
	if (!$row){  // Remplir la table de notation
		if ($note!=='-1') // annulation d'un vote -> ne pas creer un id !
			$id_notation = notation_inserer($type,$id_objet);
	} else {
		$id_notation = $row['id_notation'];
	}

	if ($id_notation){
		if ($note=='-1'){ // annulation d'un vote
			notation_supprimer($id_notation);
			$id_notation = 0;
		}
		else {
			// Modifier la note
			$c = array(
				"note" => $note,
				"id_auteur" => $id_auteur,
				"ip" => $ip
			);
			notation_modifier($id_notation,$c);
		}

	}

	$res = array("editable"=>true,"message_ok"=>_T("notation:jainote"),'id_notation'=>$id_notation);

	// peut il modifier son vote ?
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$type, 'id_objet'=>$id_objet))) {
		$res['editable']=false;
	}
	return $res;
}


?>