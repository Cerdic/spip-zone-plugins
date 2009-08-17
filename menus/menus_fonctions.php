<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_type_entree($nom){
	include_spip('inc/menus');
	$dispo = menus_lister_disponibles();
	return $dispo[$nom]['nom'];
}

if (!function_exists('generer_titre_entite')){
	function generer_titre_entite($id_objet, $type_objet){
		include_spip('base/connect_sql');
		global $table_titre;
		$champ_titre = $table_titre[table_objet($type_objet)];
		if (!$champ_titre) $champ_titre = 'titre';
		$ligne = sql_fetsel(
		    $champ_titre,
		    table_objet_sql($type_objet),
		    id_table_objet($type_objet).'='.intval($id_objet)
		);
		return $ligne ? $ligne['titre'] : '';
	}
}

?>
