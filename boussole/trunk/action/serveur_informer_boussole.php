<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de renvoyer à un site utilisateur les informations complètes sur une boussole
 *
 */
function action_serveur_informer_boussole_dist(){

	// Aucune sécurisation ni autorisation:
	// -> c'est une action anonyme pouvant être appelée de l'extérieur
	// -> par contre, cette action nécessite en argument l'alias de la boussole
	$alias = _request('arg');

	if (_BOUSSOLE_ALIAS_SERVEUR AND $alias) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		include_spip('inc/config');
		$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		// Si erreur, on renvoie un id sous forme d'une balise erreur
		$erreur = '';
		$fichier_xml = '';
		if ($boussoles) {
			// Vérifier que la boussole demandée est bien disponible sur le serveur
			if (array_key_exists($alias, $boussoles)) {
				// Si la boussole n'est pas encoe en cache on retourne une erreur
				$fichier_xml = _DIR_VAR . "cache-boussoles/boussole-${alias}.xml";
				if (!file_exists($fichier_xml)) {
					$erreur = 'cache_boussole_indisponible';
					spip_log("Le fichier cache de la boussole n'est pas disponible (alias = $alias)", 'boussole' . _LOG_ERREUR);
				}
				else {
					spip_log("Information fournie sur la boussole d'alias = $alias", 'boussole' . _LOG_INFO);
				}
			}
			else {
				$erreur = 'boussole_non_hebergee';
				spip_log("Boussole non disponible sur ce serveur (alias = $alias)", 'boussole' . _LOG_ERREUR);
			}
		}
		else {
			$erreur = 'aucune_boussole_hebergee';
			spip_log("Aucune boussole disponible sur ce serveur", 'boussole' . _LOG_ERREUR);
		}

		// Envoi du fichier ou de l'erreur
		$page = recuperer_fond('actionner', array('fichier' => $fichier_xml, 'erreur' => $erreur));
		echo $page;
	}
}

?>
