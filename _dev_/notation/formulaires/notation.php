<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation_util');
include_spip('base/abstract_sql');

function formulaires_notation_charger_dist($objet, $id_objet){

	// definition des valeurs de base du formulaire
	$valeurs = array(
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'editable'=>true,
		'note'=>0,
		'note_ponderee'=>0,
		'total'=>0
	);
	$_id_objet = id_table_objet($objet);
	
	
	// on recupere l'id de l'auteur connecte, sinon ip
	if (!$id_auteur = $GLOBALS['visiteur_session']['id_auteur']) {
		$id_auteur = 0;
		$ip	= $_SERVER['REMOTE_ADDR'];
	}
	
	// on recupere la note ponderee de l'objet et le nombre de votes
		// list($valeurs["total"], $valeurs["note"], $valeurs["note_ponderee"]) 
		//	= notation_calculer_total($objet, $id_objet);
	if ($row = sql_fetsel(
			array("note", "note_ponderee", "nombre_votes"),
			"spip_notations_objets",
			"$_id_objet=" . sql_quote($id_objet)
		)) {
		$valeurs["note"] = $row['note'];
		$valeurs["note_ponderee"] = $row['note_ponderee'];
		$valeurs["total"] = $row['nombre_votes'];
	}
	
	
	// l'auteur ou l'ip a-t-il deja vote ?
	$where = array(
		"objet=" . sql_quote($objet),
		"$_id_objet=" . sql_quote($id_objet),
	);
	if ($id_auteur) 
		$where[] = "id_auteur=" . sql_quote($id_auteur);
	else 
		$where[] = "ip=" . sql_quote($ip);
	$id_notation = sql_getfetsel("id_notation","spip_notations",$where);
	

	// peut il modifier son vote ?
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$objet, 'id_objet'=>$id_objet))) {
		$valeurs['editable']=false;
	}

	return $valeurs;
}


function formulaires_notation_verifier_dist($objet, $id_objet){
	$erreurs = array();
	
	$_id_objet = id_table_objet($objet);
	
	//  s'assurer que l'objet existe bien
	// et que le champ robot n'est pas rempli
	if (!sql_countsel("spip_$objet", "$_id_objet=" . sql_quote($id_objet))
		OR (_request('content')!="")) { 
		$erreurs['message_erreur'] = ' ';
	// note dans la bonne fourchette
	} else {
		$note = intval(_request("notation-$objet$id_objet"));
		if($note<1 || $note>notation_get_nb_notes())
			$erreurs['message_erreur'] = _T('notation:note_hors_plage');
	}
	return $erreurs;
}
	
function formulaires_notation_traiter_dist($objet, $id_objet){
	$_id_objet = id_table_objet($objet);
	
	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	} else {
		$id_auteur = 0;
	}
	$ip	= $_SERVER['REMOTE_ADDR'];
	
	// recuperation des champs
	$note = intval(_request("notation-$objet$id_objet"));
	$id_donnees	= _request('notation_id_donnees');

	// Si pas inscrit : recuperer la note de l'objet sur l'IP
	// Sinon rechercher la note de l'auteur
	$where = array("$_id_objet=" . sql_quote($id_objet));
	if ($id_auteur == 0) $where[] = "id_auteur=" . sql_quote($id_auteur);
	else $where[] = "ip=" . sql_quote($ip);
	$row = sql_fetsel(
		array("id_notation", "id_auteur", "note"),
		"spip_notations",
		$where
	);

	// Premier vote
	if (!$row){  // Remplir la table de notation
		$id_notation = insert_notation();
	} else {
		$id_notation = $row['id_notation'];
	}
	
	// Modifier la note
	$c = array(			
		"objet" => $objet,
		"$_id_objet" => $id_objet,
		"note" => $note,
		"id_auteur" => $id_auteur,
		"ip" => $ip
	);
	modifier_notation($id_notation,$c);
	notation_recalculer_total($objet,$id_objet);	

	return array("editable"=>true,"message_ok"=>"");
}


function insert_notation(){
	return sql_insertq("spip_notations", array(
			"objet" => "",
			//"id_objet" => 0,
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


function autoriser_notation_modifier_dist($faire, $type, $id, $qui, $opt){
	// la config interdit de modifier la note ?
	if ($id AND !lire_config('notation/change_note'))
		return false;
		
	// sinon est-on autorise a voter ?
	$acces = notation_get_acces();
	if ($acces!='all'){
		// tous visiteur
		if ($acces=='ide' && $qui['statut']=='')
			return false;
		// auteur
		if ($acces=='aut' && !in_array($qui['statut'],array("0minirezo","1comite")))
			return false;
		// admin
		if ($acces=='adm' && !$qui['statut']=="0minirezo")
			return false;
	}
	return true;
}


// je me demande vraiment si tout cela est utile...
// vu que tout peut etre calcule en requete depuis spip_notations
function notation_recalculer_total($objet,$id_objet){
	
	list($total, $note, $note_ponderee) = notation_calculer_total($objet, $id_objet);
	
	// selection a corriger en passant a 'objet' + 'id_objet'
	$_id_objet = id_table_objet($objet);	
	
	// Mise a jour ou insertion ?
	if (!sql_countsel("spip_notations_objets", 
			array(
				"objet=" . sql_quote($objet),
				"$_id_objet=" . sql_quote($id_objet)
				))) {
		// Remplir la table de notation des objets
		sql_insertq("spip_notations_objets", array(
			"objet" => $objet,
			"$_id_objet" => $id_objet,
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total
			));
	} else {
		// Mettre ajour dans les autres cas
		sql_updateq("spip_notations_objets", array(
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total),
			array(
				"objet=" . sql_quote($objet),
				"$_id_objet=" . sql_quote($id_objet)
			));
	}
}


function notation_calculer_total($objet, $id_objet){
	// selection a corriger en passant a 'objet' + 'id_objet'
	$_id_objet = id_table_objet($objet);	
	
	// Calculer la note de l'objet
	$somme = $total = 0;
	if ($row = sql_fetsel(
			array("COUNT(note) AS nombre","SUM(note) AS somme"),
			"spip_notations",
			array(
				"objet=". sql_quote($objet),
				"$_id_objet=" . sql_quote($id_objet) ///// a changer
			))){
				
		$somme = $row['somme'];
		$total = $row['nombre'];
	}	
	$moyenne = $somme/$total;
	$moyenne = intval($moyenne*100)/100;
	$note = $moyenne; //round($moyenne);
	$note_ponderee = notation_ponderee($moyenne, $total);
	return array($total, $note, $note_ponderee);
}
?>
