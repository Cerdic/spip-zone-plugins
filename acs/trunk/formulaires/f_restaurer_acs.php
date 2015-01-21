<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/acs_restaurer');

/**
 * Formulaire CVT facs_restaurer.html
 */
function formulaires_f_restaurer_acs_charger_dist(){
	return array(
		'_dir_dump_acs' => _DIR_DUMP.'acs/',
		'fichier' => _request('fichier')
	);
}

function formulaires_f_restaurer_acs_verifier_dist(){
	$erreurs = array();
	$fichier = _request('fichier');
	$file = _DIR_DUMP.'acs/'.basename($fichier); // securite
	if (!is_file($file)) {
		$erreurs['fichier'] = _T('acs:err_fichier_absent', array('file' => joli_repertoire($file)));
	}
	return $erreurs;
}

/**
 * Restaure une sauvegarde ACS
 * @return array
 */
function formulaires_f_restaurer_acs_traiter_dist(){
	$fichier = _request('fichier');
	return acs_restaurer($fichier);
}
?>