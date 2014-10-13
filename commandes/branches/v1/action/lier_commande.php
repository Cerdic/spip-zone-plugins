<?php
/**
 * Action du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * "Lier" une commande à un objet
 *
 * Attention : il n'y a pas de table de liens spip_commandes_liens.
 * Il ne s'agit donc pas à proprement parler d'associer une commande avec un objet.
 * L'action fait appel à la fonction lier_commande_{objet} s'il y a un fichier éponyme dans /inc.
 * Dans le cas d'un auteur, la fonction remplit le champ id_auteur dans la table spip_commandes
 *
 *     #URL_ACTION_AUTEUR{lier_commande,#ID_COMMANDE/#ID_AUTEUR/auteur,#SELF}
 * 
 * @param $arg string
 *     arguments séparés par un slash "/"
 *
 *     - id_commande : identifiant de la commande
 *     - id_objet : identifiant de l'objet
 *     - objet : type d'objet
 * @return void
 */
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
