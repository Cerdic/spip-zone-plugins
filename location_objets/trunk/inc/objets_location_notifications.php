<?php
/**
 * Fonctions du plugin Commandes relatives à la référence de commande
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Pioché dans Commandes.
 * Génère un numéro unique utilisé pour remplir le champ `reference` lors de la création d'une location.
 *
 * Le numéro retourné est la date suivi de l'identifiant
 *
 * @example
 *     ```
 *     $fonction_reference = charger_fonction('locations_reference', 'inc/');
 *     ```
 *
 * @param int $id_objets_location

 * @return string
 *     reference de la commande
**/
function inc_objets_location_notifications_dist($id_objets_location, $statut, $statut_ancien, $config, $id_auteur, $date, $date_ancienne){
	if ((!$statut_ancien OR
			$statut != $statut_ancien) &&
			(isset($config['activer'])) &&
				(
					isset($config['quand']) &&
					is_array($config['quand']) &&
					in_array($statut, $config['quand'])
				) &&
				(
					$notifications = charger_fonction('notifications', 'inc', true)
				) and
			$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur=' . $id_auteur)
			) {

		//Déterminer la langue pour les notifications
		$lang = isset($row['lang']) ? $row['lang'] : lire_config('langue_site');
		lang_select($lang);

		// Determiner l'expediteur
		$options = array(
			'statut' => $statut,
			'lang' => $lang
		);
		if ($config['expediteur'] != "facteur") {
			$options['expediteur'] = $config['expediteur_' . $config['expediteur']];
		}


			// Envoyer au vendeur et au client
			$notifications('objets_location_vendeur', $id_objets_location, $options);
			if ($config['client']) {
				$options['email'] = $email;
				$notifications('objets_location_client', $id_objets_location, $options);
			}
	}
}
