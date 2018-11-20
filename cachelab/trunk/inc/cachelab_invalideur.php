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

function split_f_arg($f, $arg='') {
	if (strpos($f, ' ')) {
		$fparts = array_filter(explode(' ',$f));
		$f = array_shift($fparts);
		$arg = implode(' ', $fparts);
	}
	return array ($f, $arg);
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
global $Memoization;
// Rq : ici, le texte du cache est non zipé (cf function creer_cache dans memoization), 
// tandis que la version en cache peut être zipée (avec index 'gz').
	if  (LOG_INVALIDATION_CORE) {
		// Abondamment appelé. À part pour pas noyer les autres
		spip_log ("maj_invalideurs($fichier, &page)", "invalideur_core_maj_invalideurs");
	}

	// Pour le calcul dynamique d'une durée de cache, la fonction user
	// reçoit la *valeur* de l'une des valeurs de l'environnement (par défaut "date_creation")
	// Exemple #CACHE{1200,duree-progressive date_naissance}
	if (isset($page['entetes']['X-Spip-Methode-Duree-Cache'])) {
		$f = 'cachelab_duree_'.$page['entetes']['X-Spip-Methode-Duree-Cache'];
		list ($f, $arg) = split_f_arg($f, 'date_creation');
		if (function_exists($f)) {
			if (!isset($page['contexte'][$arg])) {
				spip_log ("#CACHE avec squelette {$page['source']} et calcul durée avec $f mais pas de '$args' dans le contexte ".print_r($page['contexte'],1), "cachelab_erreur");
				return;
			}
			$duree = $f($page['contexte'][$arg]);
			spip_log ("#CACHE $f ($arg={$page['contexte'][$arg]}) renvoie : $duree s", "cachelab");

			$page['duree'] = $duree;
			$page['entetes']['X-Spip-Cache']=$duree;

			// On garde un souvenir
			// unset ($page['entetes']['X-Spip-Methode-Duree-Cache']);

			// Comme memoization, on ajoute une heure "histoire de pouvoir tourner
			// sur le cache quand la base de donnees est plantée (à tester)"
			// TODO CORE ? changer creer_cache pour qu'il appelle maj_invalideurs *avant* d'avoir écrit le cache
			$Memoization->set($fichier, $page, 3600+$duree);
		}
		else 
			spip_log ("#CACHE duree cache : la fonction '$f' n'existe pas (arg='$arg')\n".print_r($page,1), "cachelab_erreur");
	}
	
	// Exemple : #CACHE{1200,filtre-bidouille grave} peut grave bidouiller le cache yc ses métadonnées
	if (isset($page['entetes']['X-Spip-Filtre-Cache'])) {
		$f = 'cachelab_filtre_'.$page['entetes']['X-Spip-Filtre-Cache'];
		list ($f, $arg) = split_f_arg($f);
		if (function_exists($f)) {
			spip_log ("#CACHE appelle le filtre $f ($arg)", "cachelab");
			$f($page, $arg);
			// ici rien de plus, c'est le filtre qui fait ce qu'il veut 
			// et qui peut enregistrer le résulat
		}
		else 
			spip_log ("#CACHE filtre : la fonction '$f' n'existe pas (arg='$arg')\n".print_r($page,1), "cachelab_erreur");
	}
}

//
// Exemple de durée de cache dynamique
//
// Renvoie une durée de cache trés courte pour les caches frais
// et de plus en plus longue au fur et à mesure que le cache vieillit
// Ainsi on peut écrire un filtre assurant un affichage approximatif
// et permettre à la fois d'afficher "posté il y a 16 secondes", bien précis,
// et "posté il y a 3 mois" ou "il y a 2 ans", bien suffisant en général.
//
function cachelab_duree_progapprox($date_creation) {
	$dt_creation = new DateTime($date_creation);
	if (!$dt_creation)
		return _DUREE_CACHE_DEFAUT;

	$interval = $dt_creation->diff(new DateTime('NOW'),true); // valeur absolue
	if (!$interval)
		return _DUREE_CACHE_DEFAUT;
	if ($interval->y > 2)
		return 6*30*24*3600; // 6 mois si plus de 2 ans
	if ($interval->y)
		return 30*24*3600;	// 1 mois si plus d'un an
	if ($interval->m)
		return 7*24*3600;	// 1 semaine si plus d'un mois
	if ($interval->d > 7)
		return 24*3600;		// 1 jour si plus d'une semaine
	if ($interval->d)
		return 6*3600;		// 6h si plus d'un jour
	if ($interval->h > 6)
		return 3600;		// 1h si plus de 6h
	if ($interval->h)
		return 30*60;		// 1/2h si plus d'1h
	if ($interval->i > 10)
		return 10*60;		// 10 minutes si plus de 10 minutes
	if ($interval->i)
		return 60;			// chaque minute si plus d'une minute
	return 10;				// 10secondes si moins d'une minute
}
