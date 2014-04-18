<?php
/**
 * Ce fichier contient l'action `serveur_informer_boussole` utilisée par un site client
 * pour obtenir les informations complètes sur une boussole.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Cette action permet à un site client d'interroger un serveur actif et d'obtenir en retour
 * les informations complètes sur une boussole.
 *
 * Cette action est anonyme car elle doit pouvoir être appelée par tout utilisateur.
 * Elle nécessite un seul argument, l'alias de la boussole. L'action renvoie :
 *
 * - le fichier cache XML de la boussole demandée, si il existe;
 * - un fichier XML d'erreur contenant l'id de l'erreur;
 * - rien si le serveur n'est pas actif (l'erreur doit être traitée par l'appelant).
 *
 * @note
 * 		Les cas d'erreur retournés par un serveur actif sont :
 *
 * 		- l'alias de la boussole est vide,
 * 		- la boussole demandée n'est pas hébergée par le serveur,
 * 		- le cache de la boussole n'est pas disponible,
 * 		- aucune boussole n'est hébergée par le serveur.
 *
 * @pipeline_appel declarer_boussoles
 *
 * @return void
 */
function action_serveur_informer_boussole_dist(){

	// Aucune sécurisation ni autorisation:
	// -> c'est une action anonyme pouvant être appelée de l'extérieur
	// -> par contre, cette action nécessite en argument l'alias de la boussole
	$alias_boussole = _request('arg');

	// Récupération de l'activité du serveur
	include_spip('inc/config');
	$serveur_actif = lire_config('boussole/serveur/actif') == 'on';
	$nom_serveur = lire_config('boussole/serveur/nom');

	if ($serveur_actif) {
		// Initialisation du fichier xml et de l'erreur
		$erreur = '';
		$fichier_cache = '';

		if ($alias_boussole) {
			// Acquerir la liste des boussoles prêtes à être diffusées
			include_spip('inc/config');
			$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
			$boussoles = pipeline('declarer_boussoles', $boussoles);

			// Si erreur, on renvoie un id sous forme d'une balise erreur
			if ($boussoles) {
				// Vérifier que la boussole demandée est bien disponible sur le serveur
				if (array_key_exists($alias_boussole, $boussoles)) {
					// Construire le nom du fichier cache de la boussole et vérifier qu'il existe
					// Si la boussole n'est pas encoe en cache on retourne une erreur
					include_spip('inc/cache');
					$fichier_cache = cache_boussole_existe($alias_boussole);
					if (!$fichier_cache) {
						$erreur = 'cache_boussole_indisponible';
						spip_log("Le fichier cache de la boussole n'est pas disponible (alias = $alias_boussole)", _BOUSSOLE_LOG . _LOG_ERREUR);
					}
					else {
						spip_log("Information fournie sur la boussole d'alias = $alias_boussole", _BOUSSOLE_LOG . _LOG_INFO);
					}
				}
				else {
					$erreur = 'boussole_non_hebergee';
					spip_log("Boussole non disponible sur ce serveur (alias = $alias_boussole)", _BOUSSOLE_LOG . _LOG_ERREUR);
				}
			}
			else {
				$erreur = 'aucune_boussole_hebergee';
				spip_log("Aucune boussole disponible sur ce serveur", _BOUSSOLE_LOG . _LOG_ERREUR);
			}
		}
		else {
			$erreur = 'alias_boussole_manquant';
			spip_log("Alias de la boussole non fournie au serveur $nom_serveur", _BOUSSOLE_LOG . _LOG_ERREUR);
		}

		// Envoi du fichier ou de l'erreur
		$page = recuperer_fond('actionner', array('fichier' => $fichier_cache, 'erreur' => $erreur, 'serveur' => $nom_serveur));
		echo $page;
	}
}

?>
