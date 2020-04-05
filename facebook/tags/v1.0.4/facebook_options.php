<?php
/**
 * Options au chargement du plugin Facebook
 *
 * @plugin     Facebook
 * @copyright  2016
 * @author     vertige
 * @licence    GNU/GPL
 * @package    SPIP\Facebook\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Définir les permissions de l'appication facebook
// Par défaut on demande seulement le droit de publier et de gérer les pages
// Les éléments doivent être séparé par une virgule
// On demande aussi l'email de la personne pour les inscriptions d'auteur
// via facebook
if (!defined('_FACEBOOK_PERMISSION')) {
	define('_FACEBOOK_PERMISSION', 'publish_actions, manage_pages,user_posts, publish_pages, pages_show_list, email');
}

// Définir ce que récupère la fonction facebook_profil
if (!defined('_FACEBOOK_CHAMP_PROFIL')) {
	define('_FACEBOOK_CHAMP_PROFIL', 'id,name,email');
}
