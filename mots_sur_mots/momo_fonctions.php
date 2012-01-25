<?php

// tourne le tableau (une dimension) de 180°. tiré de: http://www.softize.net/php-how-to-rotate-an-array/

function momo_tourne_tableau($first) {
	$second = array();

	array_push($second, end($first)); //set the pointer to the last element and add it to the second array

	//while we have items, get the previous item and add it to the second array
	for($i=0; $i<sizeof($first)-1; $i++){
		array_push($second, prev($first));
	}

	return $second;
}
function momo_nomme_les_chemins($tableau) {
	$nom_chemins = "hierarchie_";
	$premier_mot = $tableau[0][0];
	$liste_mots_identifiant_chemin[0] = $premier_mot;
	$nouveau_tableau = array($nom_chemins.$premier_mot => $tableau[0]);
	next ($tableau);

	$key = key($tableau);
	$val = current($tableau);
	while (list($key,$val)=each($tableau)) {
		$sous_key = key($val);
		$sous_val = current($val);
		while (list($sous_key,$sous_val)=each($val)) {
			if (!array_key_exists($nom_chemins.$sous_val,$nouveau_tableau)) {
				$nouveau_tableau = array_merge($nouveau_tableau,array($nom_chemins.$sous_val => $val));
				array_push($liste_mots_identifiant_chemin,$sous_val);
				break;
			}
		}
	}


	return array_merge(array("id_mot_hierarchies" => $liste_mots_identifiant_chemin),$nouveau_tableau);

}

?>