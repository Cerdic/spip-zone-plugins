<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/navigation_modules');

function balise_ONGLETS_ASSOCIATION_dist ($p) {
	return calculer_balise_dynamique($p, 'ONGLETS_ASSOCIATION', array());
}

function balise_ONGLETS_ASSOCIATION_stat ($args) {
	return $args; /* on se contente de faire suivre l'argument statique de la balise */
}
function balise_ONGLETS_ASSOCIATION_dyn ($titre) {
	return association_onglets($titre);
}
?>
