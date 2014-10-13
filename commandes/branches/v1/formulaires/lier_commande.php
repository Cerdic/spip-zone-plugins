<?php
/**
 * Gestion du formulaire pour "lier" une commande
 *
 * Attention : il n'y a pas de table de liens `spip_commandes_liens`.
 * Il ne s'agit donc pas à proprement parler d'associer une commande avec un objet.
 * Le formulaire fait appel à la fonction `lier_commande_{objet}` s'il y a un fichier éponyme dans `/inc`.
 * Dans le cas d'un auteur, la fonction remplit le champ id_auteur dans la table `spip_commandes`
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Formulaires
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire de "liaison" d'une commande
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $id_commande
 *     Identifiant du commande.
 * @param string $objet
 *     Identifiant du commande.
 * @param int|string $id_objet
 *     Identifiant de l'objet
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
 */
function formulaires_lier_commande_charger_dist($id_commande, $objet, $id_objet = null, $redirect=''){
	$valeurs = array(
		'recherche_objet' => '',
        '_id_commande' => $id_commande,
        'objet' => $objet,
		'id_objet' => intval($id_objet),
		'redirect' => $redirect
	);
	return $valeurs;
}

/**
 * Vérifications du formulaire de "liaison" d'une commande
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param int|string $id_commande
 *     Identifiant du commande.
 * @param string $objet
 *     Identifiant du commande.
 * @param int|string $id_objet
 *     Identifiant de l'objet
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des erreurs
 */
function formulaires_lier_commande_verifier_dist($id_commande, $objet, $id_objet = null, $redirect=''){

    $id_objet = _request('objet_id');

	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts

    //Ne pas passer en action si on a un objet clairement identifié
    if (!is_null($id_objet) && intval($id_objet))
            $erreurs = array();

	return $erreurs;
}

/**
 * Traitement du formulaire de "liaison" d'une commande
 *
 * Traiter les champs postés
 *
 * @param int|string $id_commande
 *     Identifiant du commande.
 * @param string $objet
 *     Identifiant du commande.
 * @param int|string $id_objet
 *     Identifiant de l'objet
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Retours des traitements
 */
function formulaires_lier_commande_traiter_dist($id_commande, $objet, $id_objet = null, $redirect=''){

    if (is_null($id_objet)) 
        $id_objet = _request('objet_id');

    if ($f=charger_fonction('lier_commande_'.$objet, 'inc')) {
        $f($id_commande,$id_objet);
    } else {
		spip_log("cvt_lier_commande_".$objet."_dist $arg pas compris", "commandes");
    }    

    set_request('recherche_objet');
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
