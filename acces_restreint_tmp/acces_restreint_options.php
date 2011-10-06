<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// declarer le pipeline pour le core
$GLOBALS['spip_pipeline']['accesrestreint_liste_zones_autorisees']='';

if (isset($GLOBALS['meta']["accesrestreint_base_version"])){
	// Si on n'est pas connecte, aucune autorisation n'est disponible
	// pas la peine de sortir la grosse artillerie
	if (!isset($GLOBALS['auteur_session']['id_auteur'])){
		$GLOBALS['accesrestreint_zones_autorisees'] = '';
	}
	else {
		// Pipeline : calculer les zones autorisees, sous la forme '1,2,3'
		// TODO : avec un petit cache pour eviter de solliciter la base de donnees
		$GLOBALS['accesrestreint_zones_autorisees'] =
			pipeline('accesrestreint_liste_zones_autorisees', '');
	}

	// Ajouter un marqueur de cache pour le differencier selon les autorisations
	if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur'] = '';
	$GLOBALS['marqueur'] .= ":accesrestreint_zones_autorisees="
		.$GLOBALS['accesrestreint_zones_autorisees'];
}

?>