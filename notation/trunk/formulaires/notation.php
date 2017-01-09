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
	

	// le visiteur a-t-il deja vote ?
	$id_notation = 0;
	$qui = notation_identifier_visiteur();
	if ($qui['a_vote']) {
		$id_notation = notation_retrouver_note($type, $id_objet, $qui);
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

	if (!sql_countsel($table_objet['table'], "$_id_objet=" . intval($id_objet))
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
	$qui = notation_identifier_visiteur(true);

	// recuperation des champs
	$note = intval(_request("notation-$table$id_objet"));

	// Rechercher la note de l'auteur
	$row = false;
	if ($id_notation = notation_retrouver_note($type, $id_objet, $qui)) {
		$row = sql_fetsel('id_notation, id_auteur, note', 'spip_notations', 'id_notation=' . intval($id_notation));
	}
	// verifier ici qu'on avait bien le droit de voter, car on a pu supprimer son cookie pour avoir acces au formulaire
	// sans pour autant avoir le droit de voter (idenitification par IP ou hash)
	include_spip('inc/autoriser');
	if (autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$type, 'id_objet'=>$id_objet))) {

		include_spip('action/editer_notation');
		// Premier vote
		if (!$row) {
			// Remplir la table de notation
			if ($note!=='-1') {
				// annulation d'un vote -> ne pas creer un id !
				$id_notation = notation_inserer($type, $id_objet);
			}
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
					'note' => $note,
					'id_auteur' => $qui['id_auteur'],
					'ip' => $qui['ip'],
				);
				notation_modifier($id_notation,$c);
			}

		}
	}

	$res = array(
		'editable' => true,
		'message_ok' => _T('notation:jainote'),
		'id_notation' => $id_notation
	);

	// peut il modifier son vote ?
	if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$type, 'id_objet'=>$id_objet))) {
		$res['editable']=false;
	}
	return $res;
}
