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

// le core indique : "Calcul des pages : noter dans la base les liens d'invalidation"
//
// Appelé à la fin de creer_cache
// $page est le tableau décrivant le cache qui vient d'être calculé 
// avec les clés suivantes pour ses métadonnées : 
// squelette,source,process_ins,invalideurs,entetes,duree,texte,notes,contexte,lastmodified,sig
// http://code.spip.net/@maj_invalideurs
//
// S'il y a une entete X-Spip-Methode-Duree-Cache, on récupère la méthode
// et on appelle la fonction cachelab_calcule_duree_cache_lamethode avec le paramètre $page
// On corrige alors la durée du cache avec la valeur retournée
//
function maj_invalideurs($fichier, &$page) {
	if  (LOG_INVALIDATION_CORE) {
		// Abondamment appelé. À part pour pas noyer les autres
		spip_log ("maj_invalideurs($fichier, &page)", "invalideur_core_maj_invalideurs");
	}
	if (isset($page['entetes']['X-Spip-Methode-Duree-Cache'])) {
		global $Memoization;
		// FIXME : ici, le texte est non zipé (cf function creer_cache dans memoization), 
		// alors que la version mise en cache a peut être été zipée (index gz).
		// Il faut soit reziper le texte au besoin, soit récupérer la version cachée :
		// $page = $Memoization->get($fichier);
		// Ou changer creer_cache pour qu'il appelle maj_invalideurs *avant* d'avoir écrit le cache

		$f = 'cachelab_calcule_duree_cache_'.$page['entetes']['X-Spip-Methode-Duree-Cache'];
		if (function_exists($f)) {
			$duree = $f($page);
			spip_log ("#CACHE $f (date_creation={$page['contexte']['date_creation']}) renvoie : $duree s", "cachelab");
			$page['duree'] = $duree;
			// On garde un souvenir
			// unset ($page['entetes']['X-Spip-Methode-Duree-Cache']);
			$page['entetes']['X-Spip-Cache']=$duree;

			// Comme memoization, on ajoute une heure histoire de pouvoir tourner
			// sur le cache quand la base de donnees est plantée (à tester)
			$Memoization->set($fichier, $page, 3600+$duree);
		}
		else 
			spip_log ("#CACHE duree cache : a fonction '$f' n'existe pas\n".print_r($page,1), "cachelab_erreur");
	}
}
