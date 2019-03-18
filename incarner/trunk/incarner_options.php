<?php
/**
 * Options du plugin Incarner au chargement
 *
 * @plugin     Incarner
 * @copyright  2013
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 * @package    SPIP\Incarner\Options
 */

require __DIR__ . '/vendor/autoload.php';

$GLOBALS['liste_des_authentifications']['incarner'] = 'incarner';

/* On perd le droit de changer d'auteur comme on veut après 1h sans activité.
	 C'est important parce qu'on garde ce droit même après déconnexion (pour
	 pouvoir tester le site en tant que visiteur anonyme et revenir ensuite
	 facilement au webmestre). */
if (! defined('_INCARNER_DELAI_EXPIRATION')) {
	define('_INCARNER_DELAI_EXPIRATION', 3600);
}
