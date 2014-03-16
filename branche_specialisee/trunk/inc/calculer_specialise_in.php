<?php

/**
 * Gestion des recherches de plugins par version ou branche
 *
 * @plugin SVP pour SPIP
 * @license GPL
 * @package SPIP\SVP\Recherche
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Construit le WHERE d'une requête SQL de selection des plugins ou paquets
 * compatibles avec une version ou une branche de spip.
 * 
 * Cette fonction est appelée par le critère {compatible_spip}
 * 
 * @used-by svp_compter()
 * @used-by critere_compatible_spip_dist()
 *
 * @param string $version
 *     Numéro de version de SPIP, tel que 2.0.8
 * @param string $table
 *     Table d'application ou son alias SQL
 * @param string $op
 *     Opérateur de comparaison, tel que '>' ou '='
 * @return string
 *     Expression where de la requête SQL
 */
function inc_calculer_specialise_in($id, $sauf, $types, $not) {

	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	if (isset($b[$id]))
		return $b[$id];

	// Notre branche commence par la rubrique de depart
	$branche = $r = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement, en se protegeant des references circulaires
	$maxiter = 10000;
	while ($maxiter-- AND $filles = sql_allfetsel(
					'id_rubrique',
					'spip_rubriques',
					sql_in('id_parent', $r) ." AND ". sql_in('id_rubrique', $r, 'NOT')
					)) {
		$r = join(',', array_map('reset', $filles));
		$branche .= ',' . $r;
	}

	# securite pour ne pas plomber la conso memoire sur les sites prolifiques
	if (strlen($branche)<10000)
		$b[$id] = $branche;
	return $branche;
}

function calculer_specialise_in2() {

	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	if (isset($b[$id]))
		return $b[$id];

	// Notre branche commence par la rubrique de depart
	$branche = $r = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement, en se protegeant des references circulaires
	$maxiter = 10000;
	while ($maxiter-- AND $filles = sql_allfetsel(
					'id_rubrique',
					'spip_rubriques',
					sql_in('id_parent', $r) ." AND ". sql_in('id_rubrique', $r, 'NOT')
					)) {
		$r = join(',', array_map('reset', $filles));
		$branche .= ',' . $r;
	}

	# securite pour ne pas plomber la conso memoire sur les sites prolifiques
	if (strlen($branche)<10000)
		$b[$id] = $branche;
	return $branche;
}

?>
