<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Function déterminant les champs à utiliser dans le formulaire en fonction de la configuration de CFG
 *
 * @return array Un array contenant l'ensemble des champs
 * @param int $id_auteur[optional] Dans le cas ou cette option est présente, on ne retourne que les champs autorisé à être modifiés dans la configuration
 */
function inc_inscription3_champs_formulaire_dist($id_auteur=null) {
	$config_i3 = lire_config('inscription3');
	$valeurs = array();
	if(is_numeric($id_auteur)){
		$suffixe = '_fiche_mod';
	}

	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_chargement_champs_auteurs_elargis',array());
	//charge les valeurs de chaque champs proposés dans le formulaire
	foreach ($config_i3 as $clef => $valeur) {

		/* Il faut retrouver les noms des champ,
		 * par défaut inscription3 propose pour chaque champ le cas champ_obligatoire
		 *  On retrouve donc les chaines de type champ_obligatoire
		 *  Ensuite on verifie que le champ est proposé dans le formulaire
		 *  Remplissage de $valeurs[]
		 */
		//decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
		//?: permet de rechercher la chaine sans etre retournée dans les résultats
		preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);

		if ((!empty($resultat[1])) && ($config_i3[$resultat[1].$suffixe] == 'on') && (!in_array($resultat[1],$exceptions_des_champs_auteurs_elargis)) && ($resultat[1] != 'password')) {
			$valeurs[] = $resultat[1];
		}
	}
	/**
	 * On force l'ajout du règlement si configuré (il ne passe pas avec les tests ci dessus)
	 */
	if($config_i3['reglement'] && ($action != 'charger')){
		$valeurs[] = 'reglement';
	}
	return $valeurs;
}
?>