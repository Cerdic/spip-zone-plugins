<?php

/**
 * Gestion du formulaire d'édition de rôles de liens
 *
 * @package SPIP\Formulaires
 **/
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire d'édition de rôles de liens
 *
 * #FORMULAIRE_EDITER_ROLES_OBJET_LIE{document,2,article,3}
 *   pour editer les roles du document 2 lié à l'article 3
 *
 * @param string $objet_source
 * 		objet étant associé
 * @param string|int $id_objet_source
 *		identifiant de l'objet associé
 * @param string $objet 
 * @param int|string $id_objet
 * @return array
 */
function formulaires_editer_roles_objet_lie_charger_dist($objet_source,$id_objet_source,$objet,$id_objet){

	// retourner les valeurs de editer_liens
	$table_source = table_objet($objet_source);
	$editer_liens_charger = charger_fonction('charger','formulaires/editer_liens');
	$valeurs = $editer_liens_charger($table_source,$objet,$id_objet); // documents, article, 2

	// on a besoin de id_objet_source (= document) pour inc-editer_liens_actions_roles
	if (is_array($valeurs)) {
		$valeurs = array_merge($valeurs,array('id_objet_source'=>$id_objet_source));
	}

	return $valeurs;
}

/**
 * Traiter le post des informations d'édition de rôles de liens
 *
 */
function formulaires_editer_roles_objet_lie_traiter_dist($objet_source,$id_objet_source,$objet,$id_objet){

	// retourner les traitements de editer_liens
	$table_source = table_objet($objet_source);
	$editer_liens_traiter = charger_fonction('traiter','formulaires/editer_liens');
	$res = $editer_liens_traiter($table_source,$objet,$id_objet); // documents, article, 2

	return $res;

}

