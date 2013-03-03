<?php
/**
 * Action permettant de renvoyer à un site utilisateur les informations complètes sur une boussole
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_serveur_informer_boussole_dist(){

	// Securisation: aucune car c'est une action anonyme pouvant être appeler de l'extérieur
	$alias = _request('arg');

	if ($alias) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		$boussoles = array();
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		if ($boussoles) {
			// Vérifier que la boussole demandée est bien disponible sur le serveur
			if (array_key_exists($alias, $boussoles)) {
				// Si la boussole n'est pas encoe en cache on le crée
				$xml = _DIR_VAR . "cache-boussoles/boussole-${alias}.xml";
				if (!file_exists($xml)) {
					// TODO : ajouter la création du cache ou alors on renvoie rien
					spip_log("Le fichier cache de la boussole n'est pas disponible (alias = $alias)", 'boussole' . _LOG_ERREUR);
				}
				else {
					$page = recuperer_fond('informer', array('alias' => $alias, 'xml' => $xml));
					echo $page;
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
