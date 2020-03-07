<?php
/**
 * Action du plugin Commandes d'abonnements
 *
 * @plugin     Commandes d'abonnements
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes_abonnements\Action
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Abandonne une commande d'abonnement, qu'elle soit en session ou déjà en cours dans la base
 *
 * Pas besoin de paramètre, on sait retrouver la commande automatiquement.
 * @example
 * ```
 * [(#BOUTON_ACTION{
 *   Annuler,
 *   #URL_ACTION_AUTEUR{abandonner_commande_abonnement,'',#SELF}
 * })]
 *```
 *
 * @param $arg string
 * @return void
 */
function action_abandonner_commande_abonnement_dist() {

	// Pas de paramètre, mais on sécurise
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	include_spip('inc/session');

	// Vider la commande en session
	session_set('commande_abonnement', '');

	// Abandonner la commande éventuelle en base
	// On prend celle de l'auteur connecté, sinon celle en session
	$id_auteur_session = intval(session_get('id_auteur'));
	$id_commande_session = intval(session_get('id_commande'));
	if ($id_auteur_session) {
		$id_commande = sql_getfetsel(
			'c.id_commande',
			'spip_commandes AS c INNER JOIN spip_commandes_details AS d ON c.id_commande = d.id_commande',
			array(
					'd.objet=' . sql_quote('abonnements_offre'),
					'c.statut=' . sql_quote('encours'),
					'c.id_auteur=' . $id_auteur_session,
			),
			'',
			'c.date DESC'
		);
	} elseif ($id_commande_session) {
		$id_commande = sql_getfetsel(
			'c.id_commande',
			'spip_commandes AS c INNER JOIN spip_commandes_details AS d ON c.id_commande = d.id_commande',
			array(
					'd.objet=' . sql_quote('abonnements_offre'),
					'c.statut=' . sql_quote('encours'),
					'c.id_commande=' . $id_commande_session,
			),
			'',
			'c.date DESC'
		);
	}
	if ($id_commande) {
		$abandonner_commande = charger_fonction('abandonner_commande', 'action');
		$abandonner_commande($id_commande);
	}
}
