<?php

/**
 * Compat ascendante pour d'autre plugins
 * http://zone.spip.org/trac/spip-zone/changeset/36546
 */

function agenda_verifier_corriger_date_saisie($suffixe,$horaire,&$erreurs){
	include_spip('inc/date_gestion');
	return verifier_corriger_date_saisie($suffixe,$horaire,$erreurs);
}


/**
 * Calcul d'une hierarchie
 * (liste des id_rubrique contenants une rubrique donnee)
 * (contrairement a la fonction calcul_branche_in du core qui calcule les
 * rubriques contenues)
 *
 * @param mixed $id
 * @return string
 */
function calcul_hierarchie_in($id) {

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));

	// Notre branche commence par la rubrique de depart
	$hier = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($parents = sql_allfetsel('id_parent', 'spip_rubriques',
	sql_in('id_rubrique', $id))) {
		$id = join(',', array_map('reset', $parents));
		$hier .= ',' . $id;
	}

	return $hier;
}

?>