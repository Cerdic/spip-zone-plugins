<?php
/**
 * Plugin Momo pour Spip 2.0
 * Licence GPL
 *
 */

/**
 * Ajout du bloc d'attribution de mot-clé
**/
function momo_affiche_milieu($flux) {

	if ($flux["args"]["id_mot"] and $flux["args"]["exec"] =='mots_edit') {
		$contexte = array('id_mot'=>$flux["args"]["id_mot"]);
		$fond = recuperer_fond("prive/mots_parents_mot", $contexte);
		$flux["data"] .= $fond;
	}
        return $flux;
    }

?>