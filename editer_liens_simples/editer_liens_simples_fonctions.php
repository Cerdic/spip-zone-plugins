<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010- Ateliers CYM
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Lister les id objet_source associes a l'objet id_objet
 * via la table de lien objetl_objets
 * Utilise pour les listes de #FORMULAIRE_EDITER_LIENS_SIMPLE
 *
 * @param string $objet_source
 * @param string $objet
 * @param int $id_objet
 * @param string $objet_lien
 * @return array
 */
function lister_objets_lies_simples($objet_source,$objet,$id_objet,$objet_lien){
	include_spip('action/editer_liens_simples');
	$l = array();
	if ($objet_lien==$objet){
		$res = objet_trouver_liens_simples(array($objet=>$id_objet),array($objet_source=>'*'));
	}
	else{
		$res = objet_trouver_liens_simples(array($objet_source=>'*'),array($objet=>$id_objet));
	}

	while ($row = array_shift($res))
		$l[] = $row[$objet_source];

	return $l;
}
?>
