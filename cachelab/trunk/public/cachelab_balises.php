<?php

/**
 * Surcharge DE la balise `#CACHE` definissant la durée de validité du cache du squelette
 *
 * Signature : `#CACHE{duree[,type]}`
 *
 * Le premier argument est la durée en seconde du cache. Le second
 * (par défaut `statique`) indique le type de cache :
 *
 * - `cache-client` autorise gestion du IF_MODIFIED_SINCE
 * - `statique` ne respecte pas l'invalidation par modif de la base
 *   (mais s'invalide tout de même à l'expiration du delai)
 * - `calcul-methode` où la partie `methode` est variable et indique 
 *    la méthode de calcul dynamique de la durée cache à partir 
 *    de son contenu yc ses métadonnées et notamment l'env
 *    Dans ce cas le 1er argument sert seulement pour compatibilité 
 *    si on désactive cachelab
 *
 * @balise
 * @see ecrire/public/cacher.php
 * @see memoization/public/cacher.php
 * @link http://www.spip.net/4330
 * @example
 *     ```
 *     #CACHE{24*3600}
 *     #CACHE{24*3600, cache-client}
 *     #CACHE{0} pas de cache
 *     #CACHE{3600,calcul-progressif}
 *     ```
 * @note
 *   En absence de cette balise la durée du cache est donnée
 *   par la constante `_DUREE_CACHE_DEFAUT`
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 **/
function balise_CACHE ($p) {
	if ($p->param) { 
		$duree = valeur_numerique($pd = $p->param[0][1][0]->texte);

		// noter la duree du cache dans un entete proprietaire
		$code = "'<'.'" . '?php header("X-Spip-Cache: '
			. $duree
			. '"); ?' . "'.'>'";

		// Remplir le header Cache-Control
		// cas #CACHE{0}
		if ($duree == 0) {
			$code .= ".'<'.'"
				. '?php header("Cache-Control: no-cache, must-revalidate"); ?'
				. "'.'><'.'"
				. '?php header("Pragma: no-cache"); ?'
				. "'.'>'";
		}

		// recuperer les parametres suivants
		$i = 1;
		while (isset($p->param[0][++$i])) {
			$pa = ($p->param[0][$i][0]->texte);

			if ($pa == 'cache-client'
				and $duree > 0
			) {
				$code .= ".'<'.'" . '?php header("Cache-Control: max-age='
					. $duree
					. '"); ?' . "'.'>'";
				// il semble logique, si on cache-client, de ne pas invalider
				$pa = 'statique';
			}

			if ($pa == 'statique'
				and $duree > 0
			) {
				$code .= ".'<'.'" . '?php header("X-Spip-Statique: oui"); ?' . "'.'>'";
			}
			
			if (strpos($pa, 'calcul-')===0) {
				$methode = substr($pa, 7);
				$ajout = ".'<'.'" . '?php header("X-Spip-Methode-Duree-Cache: '.$methode.'"); ?' . "'.'>'";
				$code .= $ajout;
				spip_log ("Méthode de calcul de la durée du cache : $methode", 'cachelab');
			}
		}
	} else {
		$code = "''";
	}
	$p->code = $code;
	$p->interdire_scripts = false;

	return $p;
}
