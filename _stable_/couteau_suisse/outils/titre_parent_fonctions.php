<?php

function cs_titre_parent($table_parent, $champ, $id) {
	// Utiliser la bonne fonction de recherche sql (fetch) selon la version de SPIP
	$fetch = function_exists('sql_fetch') ? 'sql_fetch' : 'spip_fetch_array';
	// retour nul si pas de parent a priori
	if(!$id) return '';
	// donc, requete !
	if($r = spip_query("SELECT titre FROM spip_$table_parent WHERE $champ=$id"))
		// s'il existe un champ, on le retourne
		if($row = $fetch($r)) return $row['titre'];
	// sinon, rien !
	return '';
}

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
	$p->code = "cs_titre_parent('$table_parent', '$champ_parent', intval($id))";
	return $p;
}

include_spip('public/interfaces');
global $table_des_traitements;

// un TITRE_PARENT est un TITRE !
if (!isset($table_des_traitements['TITRE_PARENT']))
	$table_des_traitements['TITRE_PARENT'] = $table_des_traitements['TITRE'];

?>