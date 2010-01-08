<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation');
include_spip('inc/notation_autorisations');
include_spip('base/abstract_sql');

function formulaires_notation_charger_dist($objet, $id_objet){

	// definition des valeurs de base du formulaire
	$valeurs = array(
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'editable'=>true,
		'_note_max' => notation_get_nb_notes(),
		'_form_id' => "-$objet$id_objet"
	);

	// l'auteur ou l'ip a-t-il deja vote ?
	// si le visiteur a une session, on regarde s'il a deja vote
	// sinon, non (la verification serieuse en cas de vote deja effectue
	// se faisant dans verifier() )
	if ($GLOBALS['visiteur_session'] AND session_get('a_vote')) {

		// on recupere l'id de l'auteur connecte, sinon ip
		if (!$id_auteur = $GLOBALS['visiteur_session']['id_auteur']) {
			$id_auteur = 0;
			$ip	= $_SERVER['REMOTE_ADDR'];
		}
	
		$where = array(
			"objet=" . sql_quote(objet_type($objet)),
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
	

		// peut il modifier son vote ?
		include_spip('inc/autoriser');
		if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$objet, 'id_objet'=>$id_objet))) {
			$valeurs['editable']=false;
		}
	}

	return $valeurs;
}


function formulaires_notation_verifier_dist($objet, $id_objet){
	$erreurs = array();
	
	//  s'assurer que l'objet existe bien
	// et que le champ robot n'est pas rempli
	$trouver_table = charger_fonction('trouver_table','base');
	$table_objet = $trouver_table($objet);
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

	// indiquer dans sa session que ce visiteur a vote
	// grace a ce cookie on dira a charger() qu'il faut regarder
	// de plus pres ce qu'il en est dans la base
	include_spip('inc/session');
	session_set('a_vote', true);

	// invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_notation/$objet/$id_objet'");

	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	} else {
		$id_auteur = 0;
	}
	$ip	= $_SERVER['REMOTE_ADDR'];
	
	// recuperation des champs
	$note = intval(_request("notation-$objet$id_objet"));
	$id_donnees	= _request('notation_id_donnees'); // ne sert a rien ?

	// Si pas inscrit : recuperer la note de l'objet sur l'IP
	// Sinon rechercher la note de l'auteur
	$objet = objet_type($objet);
	$where = array(
		"objet=" . sql_quote($objet),
		"id_objet=" . sql_quote($id_objet),
		);
	if ($id_auteur != 0) $where[] = "id_auteur=" . sql_quote($id_auteur);
	else $where[] = "ip=" . sql_quote($ip);
	$row = sql_fetsel(
		array("id_notation", "id_auteur", "note"),
		"spip_notations",
		$where
	);

	// Premier vote
	if (!$row){  // Remplir la table de notation
		if ($note!=='-1') // annulation d'un vote -> ne pas creer un id !
			$id_notation = insert_notation();
	} else {
		$id_notation = $row['id_notation'];
	}

	if ($id_notation){
		if ($note=='-1'){ // annulation d'un vote
			supprimer_notation($id_notation);
			$id_notation = 0;
		}
		else {
			// Modifier la note
			$c = array(
				"objet" => $objet,
				"id_objet" => $id_objet,
				"note" => $note,
				"id_auteur" => $id_auteur,
				"ip" => $ip
			);
			modifier_notation($id_notation,$c);
		}
	
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
		notation_recalculer_total($objet,$id_objet);
	}

	$res = array("editable"=>true,"message_ok"=>_T("notation:jainote"),'id_notation'=>$id_notation);
	
	// peut il modifier son vote ?
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$objet, 'id_objet'=>$id_objet))) {
		$res['editable']=false;
	}
	return $res;
}


function insert_notation(){
	return sql_insertq("spip_notations", array(
			"objet" => "",
			"id_objet" => 0,
			"id_auteur" => 0,
			"ip" => "",
			"note" => 0
			));
}

function modifier_notation($id_notation,$c=array()) {
	// pipeline pre edition
	sql_updateq('spip_notations',$c,'id_notation='.sql_quote($id_notation));
	// pipeline post edition
	return true;
	
}

function supprimer_notation($id_notation) {
	// pipeline pre edition
	sql_delete('spip_notations','id_notation='.sql_quote($id_notation));
	// pipeline post edition
	return true;
}



// je me demande vraiment si tout cela est utile...
// vu que tout peut etre calcule en requete depuis spip_notations
// a peu de choses pres (!)
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
		suivre_invalideur("notation/$objet/$id_objet");
			
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
			suivre_invalideur("notation/$objet/$id_objet");
		}
	}
}


function notation_calculer_total($objet, $id_objet){

	$ponderation = lire_config('notation/ponderation',30);
	
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

?>