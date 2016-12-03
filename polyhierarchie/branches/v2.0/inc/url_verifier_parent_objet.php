<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction utiliser par les urls arbo/arbopoly pour verifier que le parent est licite
 * @param string $objet
 * @param int $id_objet
 * @param string $objet_parent
 * @param int $id_objet_parent
 * @return bool
 */
function inc_url_verifier_parent_objet_dist($objet, $id_objet, $objet_parent, $id_objet_parent) {
	if ($objet_parent === 'rubrique'
		and sql_getfetsel('id_parent','spip_rubriques_liens','id_parent='.intval($id_objet_parent).' AND objet='.sql_quote($objet).' AND id_objet='.intval($id_objet))) {
		return true;
	}
	return false;
}
