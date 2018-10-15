<?php

/**
 * Invalider les caches liés à telle condition
 *
 * Les invalideurs sont de la forme 'objet/id_objet'.
 * La condition est géneralement "id='objet/id_objet'".
 *
 * Ici on se contente de noter la date de mise à jour dans les metas,
 * pour le type d'objet en question (non utilisé cependant) et pour
 * tout le site (sur la meta `derniere_modif`)
 *
 * @global derniere_modif_invalide
 *     Par défaut à `true`, la meta `derniere_modif` est systématiquement
 *     calculée dès qu'un invalideur se présente. Cette globale peut
 *     être mise à `false` (aucun changement sur `derniere_modif`) ou
 *     sur une liste de type d'objets (changements uniquement lorsqu'une
 *     modification d'un des objets se présente).
 *
 * @param string $cond
 *     Condition d'invalidation
 * @param bool $modif
 *     Inutilisé
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function suivre_invalideur($cond, $modif = true) {
	if (!$modif) {
		return;
	}

	// determiner l'objet modifie : forum, article, etc
	if (preg_match(',["\']([a-z_]+)[/"\'],', $cond, $r)) {
		$objet = objet_type($r[1]);
	}

	// stocker la date_modif_$objet (ne sert a rien pour le moment)
	if (isset($objet)) {
		ecrire_meta('derniere_modif_' . $objet, time());
	}
	spip_log("coucou", "surcharge_suivre_invalideur");

	// si $derniere_modif_invalide est un array('article', 'rubrique')
	// n'affecter la meta que si un de ces objets est modifie
	if (is_array($GLOBALS['derniere_modif_invalide'])) {
		if (in_array($objet, $GLOBALS['derniere_modif_invalide'])) {
			spip_log ("suivre_invalideur / '$objet' ($cond)", "cachelab");
			spip_log ("suivre_invalideur $objet ($cond)", "suivre_invalideur");
			ecrire_meta('derniere_modif', time());
		}
	} // sinon, cas standard, toujours affecter la meta
	else {
		ecrire_meta('derniere_modif', time());
		spip_log ("suivre_invalideur ($cond)", "cachelab");
		spip_log ("suivre_invalideur ($cond)", "suivre_invalideur");
	}
}

