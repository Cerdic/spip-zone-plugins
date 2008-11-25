<?php
// filtre 'titre_rubrique' a utiliser sur #ID_RUBRIQUE d'un objet ou #ID_PARENT d'une rubrique
// Ex :  : <BOUCLE_a(ARTICLES)>[(#ID_RUBRIQUE|titre_rubrique)]</BOUCLE_a>
// Ex :  : <BOUCLE_b(BREVES)>[(#ID_RUBRIQUE|titre_rubrique)]</BOUCLE_b>
// Ex :  : <BOUCLE_r(RUBRIQUES)>[(#ID_PARENT|titre_rubrique)]</BOUCLE_r>
function titre_rubrique($id_rubrique, $table='rubriques', $id='id_rubrique') {
	return cs_titre_id($id_rubrique, $table, $id);
}

// filtre 'titre_groupe' a utiliser sur #ID_GROUPE d'un mot-clef
// Ex :  : <BOUCLE_m(MOTS)>[(#ID_GROUPE|titre_groupe)]</BOUCLE_m>
function titre_groupe($id_mot, $table='groupes_mots', $id='id_groupe') {
	return cs_titre_id($id_mot, $table, $id);
}

// filtre 'titre_id'
// Renvoie le titre trouve dans la $table_parent, la ou $champ = $id
function cs_titre_id($id, $table_parent='rubriques', $champ='id_rubrique') {
	// retour nul si pas de parent a priori
	if(!$id) return '';
	// Utiliser la bonne requete en fonction de la version de SPIP
	if(function_exists('sql_getfetsel')) {
		// SPIP 2.0
		if($titre = sql_getfetsel('titre', "spip_$table_parent", " $champ=$id"))
			return $titre;
	} else {
		if($r = spip_query("SELECT titre FROM spip_$table_parent WHERE $champ=$id"))
			// s'il existe un champ, on le retourne
			if($row = spip_fetch_array($r)) return $row['titre'];
	}
	// sinon, rien !
	return '';
}

// Rubrique parente de tout objet possedant un 'id_rubrique' ou groupe de mot-clef
function balise_TITRE_PARENT_dist($p) {
	// examen du contexte
	switch ($p->type_requete) {
		case 'rubriques':
			$table_parent = 'rubriques';
			$champ_parent = 'id_rubrique';
			$id = 'id_parent';
			break;
		case 'mots':
			$table_parent = 'groupes_mots';
			$id = $champ_parent = 'id_groupe';
			break;
		default:
			$table_parent = 'rubriques';
			$id = $champ_parent = 'id_rubrique';
			break;
	}
	// id de l'objet a trouver pour retourner son titre
	$id = champ_sql($id, $p);
	// le code php a executer
	$p->code = "cs_titre_id(intval($id), '$table_parent', '$champ_parent')";
	return $p;
}

// juste le groupe d'un mot-clef
function balise_TITRE_GROUPE_dist($p) {
	$p->code = "''";
	return $p->type_requete=='mots'?balise_TITRE_PARENT_dist($p):$p;
}

include_spip('public/interfaces');
global $table_des_traitements;

// TITRE_PARENT et TITRE_GROUPE sont des TITREs !
if (!isset($table_des_traitements['TITRE_PARENT']))
	$table_des_traitements['TITRE_PARENT'] = $table_des_traitements['TITRE'];
if (!isset($table_des_traitements['TITRE_GROUPE']))
	$table_des_traitements['TITRE_GROUPE'] = $table_des_traitements['TITRE'];

?>