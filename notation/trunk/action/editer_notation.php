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

include_spip('base/abstract_sql');

/**
 * Inserer une nouvelle note
 *
 * @param string $objet
 * @param int $id_objet
 * @return int|bool
 */
function notation_inserer($objet, $id_objet){
	$champs = array(
		"objet" => $objet,
		"id_objet" => $id_objet,
		"id_auteur" => 0,
		"ip" => "",
		"note" => 0,
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_notations',
			),
			'data' => $champs
		)
	);

	$id_notation = sql_insertq("spip_notations", $champs);

	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_notations',
				'id_objet' => $id_notation
			),
			'data' => $champs
		)
	);

	return $id_notation;
}

/**
 * Modifier une note existante
 *
 * @param int $id_notation
 * @param array $set
 * @return bool|string
 */
function notation_modifier($id_notation, $set=array()) {
	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		array('id_auteur','ip','note'),
		// black list : on ne peut pas changer sur quoi porte une note
		array("objet","id_objet"),
		// donnees eventuellement fournies
		$set
	);

	// recuperer l'objet sur lequel porte la notation
	$t = sql_fetsel("objet,id_objet", "spip_notations", "id_notation=".intval($id_notation));
	if ($err = objet_modifier_champs('notation', $id_notation,
		array(),
		$c))
		return $err;

	// mettre a jour les stats
	//
	// cette action est presque devenue inutile
	// comme la table spip_notations_objets
	// (qui devrait s'appeler spip_notations_stats plutot !)
	// car le critere {notation} permet d'obtenir ces resultats
	// totalements a jour...
	// Cependant, quelques cas tres particuliers de statistiques
	// font que je le laisse encore, comme calculer l'objet le mieux note :
	// 	<BOUCLE_notes_pond(NOTATIONS_OBJETS){0,10}{!par note_ponderee}>
	// qu'il n'est pas possible de traduire dans une boucle NOTATION facilement.
	notation_recalculer_total($t['objet'],$t['id_objet']);

	// invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='notation/".$t['objet']."/".$t['id_objet']."'");

	return $err;
}

/**
 * Supprimer une note existante
 * 
 * @param int $id_notation
 * @return bool
 */
function notation_supprimer($id_notation) {
	// recuperer l'objet sur lequel porte la notation
	$t = sql_fetsel("objet,id_objet", "spip_notations", "id_notation=".intval($id_notation));


	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_notations',
				'id_objet' => $id_notation,
				'action'=>'supprimer',
			),
			'data' => array()
		)
	);

	sql_delete('spip_notations','id_notation='.sql_quote($id_notation));
	
	// Envoyer aux plugins
	$champs = pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_notations',
				'id_objet' => $id_notation,
				'action'=>'supprimer',
			),
			'data' => array()
		)
	);

	// mettre a jour les stats
	//
	// cette action est presque devenue inutile
	// comme la table spip_notations_objets
	// (qui devrait s'appeler spip_notations_stats plutot !)
	// car le critere {notation} permet d'obtenir ces resultats
	// totalements a jour...
	// Cependant, quelques cas tres particuliers de statistiques
	// font que je le laisse encore, comme calculer l'objet le mieux note :
	// 	<BOUCLE_notes_pond(NOTATIONS_OBJETS){0,10}{!par note_ponderee}>
	// qu'il n'est pas possible de traduire dans une boucle NOTATION facilement.
	notation_recalculer_total($t['objet'],$t['id_objet']);

	// invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='notation/".$t['objet']."/".$t['id_objet']."'");

	return true;
}


/**
 * je me demande vraiment si tout cela est utile...
 * vu que tout peut etre calcule en requete depuis spip_notations
 * a peu de choses pres (!)
 *
 * @param string $objet
 * @param int $id_objet
 * @return void
 */
function notation_recalculer_total($objet,$id_objet){

	list($total, $note, $note_ponderee) = notation_calculer_total($objet, $id_objet);

	$objet = objet_type($objet);

	// Mise a jour ou insertion ?
	if (!sql_countsel("spip_notations_objets", array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet),
				))) {
		// Remplir la table de notation des objets
		sql_insertq("spip_notations_objets", array(
			"objet" => $objet,
			"id_objet" => $id_objet,
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total
			));
		include_spip('inc/invalideur');
		suivre_invalideur("id='notation/$objet/$id_objet'");

	} else {
		$anc_note_ponderee = sql_getfetsel('note_ponderee','spip_notations_objets',array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet)
			));
		// Mettre ajour dans les autres cas
		sql_updateq("spip_notations_objets", array(
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total),
			array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet)
			));
		// on optimise en n'invalidant que si la notre ponderee change (sinon ca ne se verra pas)
		if (round($anc_note_ponderee)!=$note_ponderee){
			include_spip('inc/invalideur');
			suivre_invalideur("id='notation/$objet/$id_objet'");
		}
	}
}


/**
 * calculer un total pour un objet/id_objet
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function notation_calculer_total($objet, $id_objet){

	include_spip('inc/notation');
	$ponderation = notation_get_ponderation();

	// Calculer les moyennes
	// cf critere {notation}
	$select = array(
		'notations.objet',
		'notations.id_objet',
		'COUNT(notations.note) AS nombre_votes',
		'ROUND(AVG(notations.note),2) AS moyenne',
		// *1.0 pour forcer une division reelle sinon 4/3=1 (sql server, sqlite...)
		'ROUND(AVG(notations.note)*(1-EXP(-5*COUNT(notations.note)*1.0/'.$ponderation.')),2) AS moyenne_ponderee'
	);
	if (!$row = sql_fetsel(
			$select,
			"spip_notations AS notations",
			array(
				"notations.objet=". sql_quote(objet_type($objet)),
				"notations.id_objet=" . sql_quote($id_objet)
			),'notations.id_objet')) {
		return array(0,0,0);
	} else {
		return array($row['nombre_votes'], $row['moyenne'], $row['moyenne_ponderee']);
	}
}

// pour compat

function insert_notation($objet, $id_objet){return notation_inserer($objet, $id_objet);}
function modifier_notation($id_notation,$c=array()) {return notation_modifier($id_notation, $c);}
function supprimer_notation($id_notation) { return notation_supprimer($id_notation); }

?>
