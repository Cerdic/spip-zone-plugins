<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_commande_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
    //id_commande/id_objet/objet
	$arg = explode('/', $arg);

    $id_commande =intval($arg[0]);
    $id_objet =intval($arg[1]);
    $objet = $arg[2];

    if (is_null($objet))
        $objet = "auteur";

    if ($f=charger_fonction('lier_commande_'.$objet, 'inc')) {
        $f($id_commande,$id_objet);
    } else {
		spip_log("action_lier_commande_".$objet."_dist $arg pas compris","commandes");
    }

}

?>
