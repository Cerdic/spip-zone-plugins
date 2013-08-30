<?php
/**
 * Ce fichier contient l'action serveur_lister_boussoles utilisée par un site client
 * pour obtenir la liste des boussoles hébergée par le serveur.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de renvoyer à un site utilisateur la liste des boussoles disponibles sur un serveur
 *
 */
/**
 * Cette action permet à un site client d'interroger un serveur actif et d'obtenir en retour
 * la liste des boussoles qu'il héberge.
 *
 * Cette action est anonyme car elle doit pouvoir être appelée par tout utilisateur.
 * Elle nécessite aucun argument. L'action renvoie :
 *
 * - le fichier cache XML de la liste des boussoles, si il existe;
 * - un fichier XML d'erreur contenant l'id de l'erreur;
 * - rien si le serveur n'est pas actif (l'erreur doit être traitée par l'appelant).
 *
 * @note
 * 		Les cas d'erreur retournés par un serveur actif sont :
 *
 * 		- le cache de la liste des boussoles n'est pas disponible,
 * 		- aucune boussole n'est hébergée par le serveur.
 *
 * @pipeline_appel declarer_boussoles
 *
 * @return void
 */
function action_serveur_lister_boussoles_dist(){

	// Aucune sécurisation ni autorisation:
	// -> c'est une action anonyme pouvant être appelée de l'extérieur

	// Récupération de l'activité du serveur
	include_spip('inc/config');
	$serveur_actif = lire_config('boussole/serveur/actif') == 'on';
	$nom_serveur = lire_config('boussole/serveur/nom');

	if ($serveur_actif) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		include_spip('inc/config');
		$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		// Si erreur, on renvoie un id sous forme d'une balise erreur
		$erreur = '';
		$fichier_liste = '';
		if ($boussoles) {
			// Vérifier que le cache existe
			$fichier_liste = _DIR_VAR . "cache-boussoles/boussoles.xml";
			if (!file_exists($fichier_liste)) {
				$erreur = 'cache_liste_indisponible';
				spip_log("Le fichier cache de la liste des boussoles n'est pas disponible (alias = $alias)", 'boussole' . _LOG_ERREUR);
			}
			else {
				spip_log("Liste des boussoles disponibles fournie", 'boussole' . _LOG_INFO);
			}
		}
		else {
			$erreur = 'aucune_boussole_hebergee';
			spip_log("Aucune boussole disponible sur ce serveur", 'boussole' . _LOG_ERREUR);
		}

		$page = recuperer_fond('actionner', array('fichier' => $fichier_liste, 'erreur' => $erreur, 'serveur' => $nom_serveur));
		echo $page;
	}
}

?>
