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
		if (!$objet) {
			// stocker la date_modif_extra_$extra (ne sert a rien)
			ecrire_meta('derniere_modif_extra_' . $r[1], time());
			$f="cachelab_suivre_invalideur_{$r[1]}";
		}
		else {
			// stocker la date_modif_$objet (ne sert a rien)
			ecrire_meta('derniere_modif_' . $objet, time());
			$f="cachelab_suivre_invalideur_$objet";
		}
		if (function_exists($f)) {
			spip_log ("suivre_invalideur appelle $f($cond,$modif)", "cachelab");
			$modif = $f($cond, $modif);	 // $f renvoie la nouvelle valeur de $modif
			// si l'invalidation a été totalement traitée par $f, ya plus rien à faire
			if (!$modif)
				return;
		}
	}

	// si $derniere_modif_invalide est un array('article', 'rubrique')
	// n'affecter la meta que si c'est un de ces objets qui est modifié
	if (is_array($GLOBALS['derniere_modif_invalide'])) {
		if (in_array($objet, $GLOBALS['derniere_modif_invalide'])) {
			include_spip ('inc/cachelab');
			cachelab_filtre('del');
			spip_log ("suivre_invalideur / objet invalidant : '$objet' ($cond)", "cachelab");
			spip_log ("suivre_invalideur / objet invalidant : '$objet' ($cond)", "suivre_invalideur");
			ecrire_meta('derniere_modif', time());
		}
		else
			spip_log ("NON invalidant : $cond", "suivre_invalideur");

	} // sinon, cas standard du core, toujours affecter la meta et tout effacer
	else {
		ecrire_meta('derniere_modif', time());
		include_spip ('inc/cachelab');
		cachelab_filtre('del');
		spip_log ("suivre_invalideur standard / objet '$objet' ($cond)", "cachelab");
		spip_log ("suivre_invalideur standard / objet '$objet' ($cond)", "suivre_invalideur");
		// et tout effacer
	}
}
