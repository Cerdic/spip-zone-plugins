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
function corbeille_formulaires_reponses_pre_suppression_dist($ids) {
	foreach ($ids as $id_formulaires_reponse){
		$id_formulaire = sql_getfetsel('id_formulaire', 'spip_formulaires_reponses',"id_formulaires_reponse=".$id_formulaires_reponse);
		formidable_effacer_fichiers_reponse($id_formulaire, $id_formulaires_reponse);	
		sql_delete('spip_formulaires_reponses_champs', "id_formulaires_reponse=$id_formulaires_reponse");
		spip_log("Effacement des champs de la réponse $id_formulaires_reponse via la corbeille", "formidable");
	}
}


