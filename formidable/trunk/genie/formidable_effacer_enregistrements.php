<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable_fichiers');
/**
 * Effacer régulièrement les enregistrements obsolètes
**/
function genie_formidable_effacer_enregistrements($t) {
	$res = sql_select("id_formulaire,traitements","spip_formulaires");
	while ($result = sql_fetch($res)) {
		$traitements = unserialize($result['traitements']);
		$id_formulaire = $result['id_formulaire'];
		if (
			isset ($traitements['enregistrement']['effacement']) 
		  and $traitements['enregistrement']['effacement']=='on' 
			and isset($traitements['enregistrement']['effacement_delai'])
		) {
			$delai = intval($traitements['enregistrement']['effacement_delai']);

			if ($delai > 0) { 
				// si on a bien configuré un délai
				$asupprimer = sql_select(
					'id_formulaires_reponse', 
					'spip_formulaires_reponses',
					"DATE_SUB(CURDATE(), INTERVAL $delai DAY) > maj 
						AND id_formulaire = $id_formulaire"
				); // on utilise la date de maj, ce qui fait que lorsqu'une réponse est modifiée, l'échéance de la suppression est prorogée
				while ($reponse = sql_fetch($asupprimer)){
					$id_formulaires_reponse = $reponse['id_formulaires_reponse'];

					formidable_effacer_fichiers_reponse($id_formulaire, $id_formulaires_reponse); // effacer d'abord les fichiers
					sql_delete('spip_formulaires_reponses',"id_formulaires_reponse=$id_formulaires_reponse");// les réponses
					sql_delete('spip_formulaires_reponses_champs',"id_formulaires_reponse=$id_formulaires_reponse");//les champs correspondant
					spip_log("Effacement de la réponse $$id_formulaires_reponse du form $id_formulaire car antérieur à $delai jours");
				}
			}	
		}
	}
	return 1;
}
