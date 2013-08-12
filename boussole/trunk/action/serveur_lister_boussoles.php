<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de renvoyer à un site utilisateur la liste des boussoles disponibles sur un serveur
 *
 */
function action_serveur_lister_boussoles_dist(){

	// Aucune sécurisation ni autorisation:
	// -> c'est une action anonyme pouvant être appelée de l'extérieur

	if (_BOUSSOLE_ALIAS_SERVEUR) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		$boussoles = $GLOBALS['serveur_boussoles_disponibles'];
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		if ($boussoles) {
			// Vérifier que le cache existe
			$fichier_liste = _DIR_VAR . "cache-boussoles/boussoles.xml";
			if (!file_exists($fichier_liste)) {
				spip_log("Le fichier cache de la boussole n'est pas disponible (alias = $alias)", 'boussole' . _LOG_ERREUR);
			}
			else {
				$page = recuperer_fond('actionner', array('fichier' => $fichier_liste));
				echo $page;
				spip_log("Liste des boussoles disponibles fournie", 'boussole' . _LOG_INFO);
			}
		}
		else
			spip_log("Aucune boussole disponible sur ce serveur", 'boussole' . _LOG_ERREUR);
	}
}

?>
