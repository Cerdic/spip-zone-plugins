<?php
//
// Fonctions définies par invalideur.php dans le core,
// et surchargées par CacheLab
//

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/cachelab_utils');

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


function suivre_invalideur($cond, $modif = true) {
	if (!$modif) {
		return;
	}

	$objet='';
	// determiner l'objet modifie : forum, article, etc
	if (preg_match(',["\']([a-z_]+)[/"\'],', $cond, $r)) {
		$objet = objet_type($r[1]);
		if (!$objet) {	// cas par exemple de 'recalcul' ?
			spip_log("suivre_invalideur avec typesignal {$r[1]} sans objet_type", 'cachelab_signal_exotique_ou_erreur');
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

	// affecter la meta si $derniere_modif_invalide est un array (de types d'objets)
	// et que c'est un de ces objets qui est modifié
	// OU bien si ce n'est pas un array
	if (!is_array($GLOBALS['derniere_modif_invalide'])
		or ($objet
			and in_array($objet, $GLOBALS['derniere_modif_invalide']))) {
		ecrire_meta('derniere_modif', time());
		include_spip ('inc/cachelab');
		spip_log ("invalidation totale / signal '$cond' avec objet '$objet'", "suivre_invalideur");
	}
	else 
		spip_log ("invalidation évitée : $cond", "cachelab_not");
}

//
// Surcharge de maj_invalideurs
// le core indique : "Calcul des pages : noter dans la base les liens d'invalidation"
//
// Appelé à la fin de creer_cache
// $page est le tableau décrivant le cache qui vient d'être calculé 
// avec les clés suivantes pour ses métadonnées : 
// squelette,source,process_ins,invalideurs,entetes,duree,texte,notes,contexte,lastmodified,sig
// http://code.spip.net/@maj_invalideurs
//
// S'il y a une entete X-Spip-Methode-Duree-Cache on récupère la méthode
// et on appelle la fonction cachelab_calcule_duree_cache_lamethode 
// avec en argument la valeur de l'argument dans l'envt ou de date_creation par défaut
// On corrige alors la durée du cache avec la valeur retournée.
//
// S'il y a une entete X-Spip-Filtre-Cache on récupère le filtre
// et on l'appelle avec le cache entier en argument
// Le filtre peut modifier n'importe quelle partie du cache, métadonnée ou résultat de compilation.
//

// define ('LOG_INVALIDATION_CORE', true);
function maj_invalideurs($fichier, &$page) {
global $Memoization;
// Rq : ici, le texte du cache est non zipé (cf function creer_cache dans memoization), 
// tandis que la version en cache peut être zipée (avec index 'gz').
	if  (LOG_INVALIDATION_CORE) {
		// Abondamment appelé. À part pour pas noyer les autres
		spip_log ("maj_invalideurs($fichier, &page)", "invalideur_core_maj_invalideurs");
		spip_log ("maj_invalideurs($fichier, &page)\n".print_r($page,1), "invalideur_core_maj_invalideurs_details");
	};

static $var_cache;
	$infos = $hint_squel = '';
	if (!isset($var_cache))
		$var_cache = _request('var_cache');
	if ($var_cache=='sessionnement') // on veut le sessionnement seul à l'écran
		$hint_squel = ' title="'.attribut_html($page['source']).'" ';
	else
		$infos = $page['source'];		// on prépare les infos supplémentaires

	// Pour le calcul dynamique d'une durée de cache, la fonction user
	// reçoit la *valeur* de l'une des valeurs de l'environnement (par défaut "date_creation")
	// Exemple #CACHE{1200,duree-progressive date_naissance}
	if (isset($page['entetes']['X-Spip-Methode-Duree-Cache'])) {
		$f = 'cachelab_duree_'.$page['entetes']['X-Spip-Methode-Duree-Cache'];
		list ($f, $arg) = split_first_arg($f, 'date_creation');
		if (function_exists($f)) {
			if (!isset($page['contexte'][$arg])) {
				spip_log ("#CACHE avec squelette {$page['source']} et calcul de durée avec $f mais pas de '$arg' dans le contexte ".print_r($page['contexte'],1), "cachelab_erreur");
				return;
			}
			$duree = $f($page['contexte'][$arg]);
			if (!defined('LOG_CACHELAB_DUREES_DYNAMIQUES') or LOG_CACHELAB_DUREES_DYNAMIQUES)
				spip_log ("#CACHE $f ($arg={$page['contexte'][$arg]}) renvoie : $duree s", "cachelab");

			if ($var_cache)
				echo "<div class='cachelab_blocs' $hint_squel><h6>Durée dynamique : $duree</h6><small>$infos</small></div>";

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
		list ($f, $arg) = split_first_arg($f);
		if (function_exists($f)) {
			if (!defined('LOG_CACHELAB_FILTRES') or LOG_CACHELAB_FILTRES)
				spip_log ("#CACHE appelle le filtre $f ($arg)", "cachelab");
			$toset = $f($page, $arg);
			// Le filtre renvoie un booléen qui indique s'il faut mémoizer le cache
			if ($toset)
				$Memoization->set($fichier, $page, $cache['entete']['X-Spip-Cache']);
		}
		else 
			spip_log ("#CACHE filtre : la fonction '$f' n'existe pas (arg='$arg')\n".print_r($page,1), "cachelab_erreur");
	}
	
	if ($var_cache)
		echo '<div class="cachelab_blocs" '.$hint_squel.'><h6>Sessionnement : '
				.cachelab_etat_sessionnement($page['invalideurs'], 'précis')
			 .'</h6><small>'.$infos.'</small></div>';
}
