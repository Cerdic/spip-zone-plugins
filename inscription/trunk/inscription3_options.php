<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 * 
 * Fichier des options spécifiques du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajout du statut 8aconfirmer dans la liste des statuts possibles de SPIP
 * Des exemples dans i3_validation_methods.js.html
 * cf : crayons_validation.js.html
 */
$GLOBALS['liste_des_statuts']['inscription3:info_aconfirmer'] = "8aconfirmer";

function envoyer_inscription($desc, $nom, $mode, $id) {
	return false;
}
?>