<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Détecter une commande payée pour activer un abonnement
 *
 * @pipeline post_edition
 * @param array $flux
 * 		Contenu du pipeline
 * @return array
 * 		Contenu du pipeline modifié
 */
function commande2abonnement_post_edition($flux) {
	
}
