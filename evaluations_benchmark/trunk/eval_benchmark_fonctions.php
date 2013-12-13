<?php

// obtenir la liste des identifiants de mots cles lies a notre objet...
function valeur_champ_ctags($table, $id, $champ) {
	$valeurs = sql_allfetsel("m.id_mot", "spip_mots AS m, spip_mots_${table}s AS ma",
		array("m.id_groupe=1", "m.id_mot = ma.id_mot", "ma.id_${table}=".sql_quote($id)));
	$valeurs = array_map('array_shift', $valeurs);
	return $valeurs;
}


// la revision du crayon ctags doit supprimer ou ajouter des liaisons de mots cles
function ctags_revision($id_objet, $colonnes, $type_objet){

	// actuellement en bdd
	$old = valeur_champ_ctags($type_objet, $id_objet, 'ctags');
	// ceux qu'on veut maintenant
	$new = explode(',', $colonnes['ctags']);
	// les mots Ã  supprimer
	$del = array_diff($old, $new);
	// les mots Ã  ajouter
	$add = array_diff($new, $old);

	// actions !
	if ($del) {
		sql_delete("spip_mots_${type_objet}s",
			array(sql_in("id_mot", $del), "id_$type_objet=$id_objet"));
	}
	if ($add) {
		$adds = array();
		foreach ($add as $a) {
			$adds[] = array(
				"id_$type_objet" => $id_objet,
				"id_mot" => $a,
			);
		}
		sql_insertq_multi("spip_mots_${type_objet}s", $adds);
	}

	return true;
}

?>
