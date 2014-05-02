<?php
/**
 * Fonction du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Notifications
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Détermine le ou les destinataires vendeurs pour les  notifications d'une commande
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @param array $options
 *     options
 * @return array
 *     Liste des destinataires
 */
function notifications_commande_vendeur_destinataires_dist($id_commande, $options) {
	include_spip('inc/config');
	$config = lire_config('commandes');
	$destinataire = $config['vendeur_'.$config['vendeur']];
	return is_array($destinataire) ? $destinataire : array($destinataire);
}

?>
