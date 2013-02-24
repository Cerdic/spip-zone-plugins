<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

 /**
 * Surcharge de inc_preselectionner_parent_nouvel_objet_dist lors de la création d'un article si la rubrique n'est pas définie
 * Renvoie l'id de la rubrique preférée si renseigné pour l'auteur en cours, sinon la première rubrique que l'auteur administre (donc dans laquelle il peut publier)
 *
 * @param string $objet
 * @param array $row
 * @return string
 */
function inc_preselectionner_parent_nouvel_objet($objet, $row){
	if ($objet == 'article') {
		$qui = $GLOBALS['visiteur_session'] ? $GLOBALS['visiteur_session'] : array('statut' => '', 'id_auteur' =>0, 'webmestre' => 'non');
		include_spip('inc/autoriser');
		$qui['restreint'] = liste_rubriques_auteur($qui['id_auteur']);
		$res = sql_select("rubrique_preferee", "spip_auteurs", "id_auteur=".$qui['id_auteur']);
		$id_rubrique = reset(picker_selected(sql_fetch($res),"rubrique"));
		$id_rubrique = $id_rubrique ? $id_rubrique : reset($qui['restreint']);
		return $id_rubrique;
	} else {
		include_spip('inc/preselectionner_parent_nouvel_objet');
		return(inc_preselectionner_parent_nouvel_objet_dist($objet, $row));
	}
}


?>
