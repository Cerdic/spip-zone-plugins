<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_sauvegarder');

/**
 * Charger formulaire CVT f_sauvegarder_acs.html
 */
function formulaires_f_sauvegarder_acs_charger_dist(){	
	$valeurs = array(
			'nom_sauvegarde' => acs_nom_sauvegarde(),
			'_dir_dump_acs' => _DIR_DUMP.'acs/'
	);
	return $valeurs;
}

/**
 * Verifications
 * @return array $erreurs
 */
function formulaires_f_sauvegarder_acs_verifier_dist() {
	$erreurs = array();
	if (!$nom = _request('nom_sauvegarde'))
		$erreurs['nom_sauvegarde'] = _T('info_obligatoire');
	elseif (!preg_match(',^[\w_][\w_.]*$,', $nom)
		OR basename($nom) !== $nom)
		$erreurs['nom_sauvegarde'] = _T('dump:erreur_nom_fichier');
	return $erreurs;
}

/**
 * Sauvegarde
 * @return array
 */
function formulaires_f_sauvegarder_acs_traiter_dist(){
	$nom_sauvegarde = _request('nom_sauvegarde');
	return acs_sauvegarder($nom_sauvegarde);
}
?>