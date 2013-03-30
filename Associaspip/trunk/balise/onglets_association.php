<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('association_modules');

function balise_ONGLETS_ASSOCIATION_dist ($p) {
	return calculer_balise_dynamique($p, 'ONGLETS_ASSOCIATION', array());
}

function balise_ONGLETS_ASSOCIATION_stat ($args) {
	return $args; // on se contente de faire suivre l'argument statique de la balise
}

function balise_ONGLETS_ASSOCIATION_dyn ($titre='', $top_exec='') {
	return association_navigation_onglets($titre, $top_exec, FALSE);
}

?>