<?php

/**
 * Function déterminant les champs obligatoires de I2 en fonction de la configuration de CFG
 *
 * @return array Un array contenant l'ensemble des champs
 * @param int $id_auteur[optional] Dans le cas ou cette option est présente, on ne retourne que les champs autorisé à être modifiés dans la configuration
 */

function inc_inscription2_champs_obligatoires_dist() {
	$valeurs = array();
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());

	//spip_log($exceptions_des_champs_auteurs_elargis,"logilog"); // vide, encore un coup de Kent1 ??

	//charge les valeurs de chaque champs proposés dans le formulaire
	foreach (lire_config('inscription2') as $clef => $valeur) {

		/*  On retrouve donc les chaines de type champ_obligatoire
		 *  Remplissage de $valeurs[]
		 */
		//decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
		//?: permet de rechercher la chaine sans etre retournée dans les résultats
		preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);

		if ((!empty($resultat[0])) && (lire_config('inscription2/'.$resultat[0]) == 'on') && (!in_array($resultat[1],$exceptions_des_champs_auteurs_elargis)) && ($resultat[1] != 'password') && ($resultat[1] != 'email') && ($resultat[1] != 'pass')) {
			$valeurs[] = $resultat[1];
		}
	}

	return $valeurs;
}
?>