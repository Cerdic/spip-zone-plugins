<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'erreur_401_message' => 'Vous n’avez pas le droit d’accéder à cette ressource.',
	'erreur_404_titre' => 'Accès interdit',
	'erreur_404_message' => 'Vous avez demandez une ressource qui n’existe pas ou qui n’a pas été trouvée.',
	'erreur_404_titre' => 'La ressource n’a pas été trouvée',
	'erreur_415_message' => 'Votre requête est dans un format inconnu, non supporté par ce serveur.',
	'erreur_415_titre' => 'Format inconnu',
);
