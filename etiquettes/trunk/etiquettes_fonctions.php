<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function etiquettes_position_quot($valeur){

	if (($position = strpos($valeur, "&quot;")) === false)
		return 100000;
	else
		return $position;

}

function ajouter_etiquettes($texte, $id, $groupe_defaut='tags', $type, $id_type, $clear){

	include_spip('inc/tag-machine');
	$clear = ($clear == "true");
	ajouter_liste_mots($texte, $id, $groupe_defaut, $type, $id_type, $clear);

}

// Hack for old php versions to use boolval()
if (!function_exists('boolval')) {
	function boolval($val) {
		return (bool) $val;
	}
}

?>
