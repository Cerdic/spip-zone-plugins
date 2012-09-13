<?php
/**
 * Plugin Pages pour mobiles
 * (c) 2012 C. Imberti, B. Marne, JM. Labat
 * Licence Creative commons BY-NC-SA
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Tableau des sous-répertoires de squelettes autorisés
 * (c'est à dire correspondant aux types de mobiles
 * et aux groupes de mobiles)
 *  
 * @return array (repertoires_possibles) 
 */
function pages_mobiles_repertoires_autorises() {
	//On fusionne les nom des mobiles et des groupes de mobiles
	$liste_repertoires =
			array_merge(
				array_keys(pages_mobiles_groupes_mobiles()),
					array_values(pages_mobiles_types_mobiles())
			);
	// On rajoute le repertoire par défaut
	array_push ($liste_repertoires,"mobile");
	return $liste_repertoires;
}

/**
 * Vérifie que le chemin passé est valide
 *  (c'est à dire sous la forme type_ou_groupe_ou_mobile/objet)
 *
 * @param string le chemin a tester
 * @return string (le chemin validé) ou false 
 */
function pages_mobiles_verification_chemin($chemin) {
	$return = false;
	// On cherche le bloc qui précède le dernier slash
	$arbo = substr($chemin,0,strrpos($chemin,"/"));
	if ($arbo AND in_array($arbo,pages_mobiles_repertoires_autorises())) $return = $chemin;
	return $return;
}

?>