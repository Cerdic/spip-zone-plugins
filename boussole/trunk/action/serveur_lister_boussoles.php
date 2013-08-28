<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de renvoyer à un site utilisateur la liste des boussoles disponibles sur un serveur
 *
 */
function action_serveur_lister_boussoles_dist(){

	// Aucune sécurisation ni autorisation:
	// -> c'est une action anonyme pouvant être appelée de l'extérieur

	// Récupération de l'activité du serveur
	include_spip('inc/config');
	$serveur_actif = lire_config('boussole/serveur/actif') == 'on';

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

		$page = recuperer_fond('actionner', array('fichier' => $fichier_liste, 'erreur' => $erreur));
		echo $page;
	}
}

?>
