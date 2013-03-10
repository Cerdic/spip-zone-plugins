<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de renvoyer à un site utilisateur les informations complètes sur une boussole
 *
 */
function action_serveur_informer_boussole_dist(){

	// Aucune securisation: c'est une action anonyme pouvant être appeler de l'extérieur
	$alias = _request('arg');

	if (_BOUSSOLE_ALIAS_SERVEUR AND $alias) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		$boussoles = $GLOBALS['serveur_boussoles_disponibles'];
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		// TODO : renvoyer un fichier avec une référence de message en cas d'erreur
		if ($boussoles) {
			// Vérifier que la boussole demandée est bien disponible sur le serveur
			if (array_key_exists($alias, $boussoles)) {
				// Si la boussole n'est pas encoe en cache on retourne une erreur
				$fichier_xml = _DIR_VAR . "cache-boussoles/boussole-${alias}.xml";
				if (!file_exists($fichier_xml)) {
					spip_log("Le fichier cache de la boussole n'est pas disponible (alias = $alias)", 'boussole' . _LOG_ERREUR);
				}
				else {
					$page = recuperer_fond('actionner', array('fichier' => $fichier_xml));
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
