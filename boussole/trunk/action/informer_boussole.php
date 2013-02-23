<?php
/**
 * Action permettant de renvoyer à un site utilisateur les informations complètes sur une boussole
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_informer_boussole_dist(){

	// Securisation: argument attendu est l'alias de la boussole
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$alias = $securiser_action();
	// TODO : en fait il faut l'alias et le prefixe

	if ($alias) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		$boussoles = array();
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		if ($boussoles) {
			// Vérifier que la boussole demandée est bien disponible sur le serveur
			if (in_array($alias, $boussoles)) {
				// Si la boussole n'est pas encoe en cache on le crée
				$xml = _DIR_VAR . "cache-boussoles/boussole-${alias}.xml"));
				if (!file_exists($xml)) {
					// Créer le cache
					// TODO : ajouter la création du cache
				}
				else {
					$page = recuperer_fond('informer', array('alias' => $alias, 'xml' => $xml));
					$x=$page;
					spip_log("Information fournie sur la boussole d'alias = $alias", 'boussole' . _LOG_INFO);
				}
			}
			else
				spip_log("Boussole non disponible sur ce serveur (alias = $alias)", 'boussole' . _LOG_ERREUR);
		}
		else
			spip_log("Aucune boussole disponible sur ce serveur", 'boussole' . _LOG_ERREUR);
	}
}

?>
