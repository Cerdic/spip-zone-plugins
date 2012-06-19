<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * La fonction à exécuter par le cron
 * On vérifie que la date de dernière modification du site soit supérieure
 * à la dernière sauvegarde
 * @param unknown_type $last
 */
function genie_saveauto_dist($last) {
	$saveauto_creation = $GLOBALS['meta']['saveauto_creation'] ? $GLOBALS['meta']['saveauto_creation'] : 0;
	$derniere_modif = max($GLOBALS['meta']['derniere_modif'],$GLOBALS['meta']['derniere_modif_rubrique'],$GLOBALS['meta']['derniere_modif_article'],$GLOBALS['meta']['derniere_modif_auteur'],$GLOBALS['meta']['derniere_modif_syndic'],$GLOBALS['meta']['derniere_modif_forum']);
	if ($derniere_modif > $saveauto_creation){
		$saveauto = charger_fonction('saveauto','inc');
		$saveauto();
	}
}

?>