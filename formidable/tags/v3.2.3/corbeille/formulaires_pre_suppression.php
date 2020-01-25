<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable');
include_spip('base/abstract_sql');

/**
 * Supprime les fichiers + les champs d'une réponse qu'on vient de supprimer avec la corbeille
 * @param array $ids
 *
**/
function corbeille_formulaires_pre_suppression_dist($ids) {
	foreach ($ids as $id_formulaire) {
		$res = sql_select('id_formulaires_reponse', 'spip_formulaires_reponses', 'id_formulaire='.intval($id_formulaire));
		while ($champ = sql_fetch($res)) {
			$id_formulaires_reponse = $champ['id_formulaires_reponse'];
			sql_delete('spip_formulaires_reponses_champs', "id_formulaires_reponse=$id_formulaires_reponse");
			sql_delete('spip_formulaires_reponses', "id_formulaires_reponse=$id_formulaires_reponse");
		}
		formidable_effacer_fichiers_formulaire($id_formulaire);
		spip_log("Effacement des réponses du formulaire $id_formulaire via la corbeille", 'formidable');
	}
}
