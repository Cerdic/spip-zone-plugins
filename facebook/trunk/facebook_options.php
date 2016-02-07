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
define('_FACEBOOK_PERMISSION', 'publish_actions, manage_pages, publish_pages, pages_show_list, email');
