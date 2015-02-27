<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("formulaires/inscription");
include_spip("inc/config");
function formulaires_inscription_avec_rubrique_charger_dist($id_rubrique=-1) {
	$mode = lire_config("rubrique_a_linscription/statut");
	$id = 0;
	return formulaires_inscription_charger_dist($mode,$id);
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
function formulaires_inscription_avec_rubrique_verifier_dist($id_rubrique=-1) {
	$mode = lire_config("rubrique_a_linscription/statut");
	$id = 0;
	return formulaires_inscription_verifier_dist($mode,$id);
}

function formulaires_inscription_avec_rubrique_traiter_dist($id_rubrique=-1) {
	$mode = lire_config("rubrique_a_linscription/statut");
	$id = 0;
	return formulaires_inscription_traiter_dist($mode,$id);
}


?>
