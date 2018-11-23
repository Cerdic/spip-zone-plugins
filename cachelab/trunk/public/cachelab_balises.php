<?php

/**
 * Surcharge de la balise `#CACHE` definissant la durée de validité du cache du squelette
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
		$i = 0;

		$descr = $p->descr;
		$sourcefile = $descr['sourcefile'];

		$t = trim($p->param[0][1][0]->texte);
		if (preg_match(',^[0-9],', $t)) {
			++$i;
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
		}

		// recuperer les parametres suivants
		// C'est analyse_resultat_skel qui transforme les headers du code en tableau $headers
		// S'il y a plusieurs fois la mm entete, seule la dernière valeur est retenue
		//
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

			$concat = (trim($code) ? '.' : '');

			if (strpos($pa, 'duree-')===0) {
				$methode = substr($pa, 6);
				$ajout = "'<'.'" . '?php header("X-Spip-Methode-Duree-Cache: '.$methode.'"); ?' . "'.'>'";
				$code .= $concat.$ajout;
				spip_log ("#CACHE $sourcefile avec méthode de calcul de la durée du cache : $methode", 'cachelab');
			}

			if (strpos($pa, 'filtre-')===0) {
				$methode = substr($pa, 7); 
				$ajout = "'<'.'" . '?php header("X-Spip-Filtre-Cache: '.$methode.'"); ?' . "'.'>'";
				$code .= $concat.$ajout; 
				spip_log ("#CACHE $sourcefile avec filtre sur le cache complet : $methode", 'cachelab');
			}
		}
	} else {
		$code = "''";
	}
	$p->code = $code;
	$p->interdire_scripts = false;

	return $p;
}


//
// Calcul de durée de cache dynamique progressive 
// adapté pour un affichage approximatif et habituel
// du type "il y a 20 secondes", "il y a 3 minutes", "ce matin",
// "hier soir", "la semaine dernière" ou "il y a 3 mois"
//
// Renvoie une durée de cache trés courte pour les caches frais
// et de plus en plus longue au fur et à mesure que le cache vieillit
// Ainsi on peut écrire un filtre assurant un affichage approximatif
// et permettre à la fois d'afficher "posté il y a 16 secondes", bien précis,
// et "posté il y a 3 mois" ou "il y a 2 ans", bien suffisant en général.
//
// usage : #CACHE{3600, duree-progapprox} ou #CACHE{3600, duree-progapprox date_naissance}
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

//
// Log tout ou un élément contenu par le tableau de cache
// dans un fichier de log dont le nom reprend le chemin du squelette
// (avec les / remplacés par des _)
//
// Exemples d'usages : 
//	#CACHE{3600, filtre-log} : log tout le cache, méta et html
//	#CACHE{filtre-log lastmodified}  : log l'entrée lastmodified du cache
// 	#CACHE{filtre-log contexte} : log tout le tableau d'environnement
//  #CACHE{filtre-log contexte/date_creation} : log l'entrée 'date_creation' de l'environnement
//
function cachelab_filtre_log($cache, $arg) {
	if (!is_array($cache) or !isset($cache['source']) or !isset($cache['lastmodified']) or !isset($cache['invalideurs'])) {
		spip_log ("cachelab_duree_progapprox ne reçoit pas un cache mais".print_r($cache,1), "cachelab_assert");
		return null;
	}
	$source = $cache['source']; 
	$source_file = str_replace(array('/','.'), '_', $source);
	$arg=trim($arg);
	if ($arg) {
		if (strpos($arg, '/')) {
			$ij=explode('/',$arg);
			$c = $cache[$i=trim(array_shift($ij))];
			$c = $c[trim($j=array_shift($ij))];
		}
		else {
			$c = $cache[$arg];
		}
	}
	else
		$c = $cache;
	spip_log ("cache[$arg] : ".print_r($c,1), "cachelab_".$source_file);
}

//
// Assertions sur le fait que le cache est sessionné ou non
// et que l'internaute est identifié ou non
//
// Arguments possibles : oui, non, login, anonyme, log
// usages :
// #CACHE{3600, filtre-assertsession non} s'assure que les emplois sont non-sessionnés
// #CACHE{filtre-assertsession oui} s'assure que tous les emplois sont sessionnés
// #CACHE{filtre-assertsession login} s'assure que tous les emplois sont sessionnés avec un internaute identifié
// #CACHE{filtre-assertsession anonyme} s'assure que tous les emplois sans internaute identifié
// Dans le cas où un assert n'est pas vérifié, un log est créé dans le fichier cachelab_assertsession
//
// Une dernière valeur de l'argument n'induit pas une assertion mais un log :
// #CACHE{filtre-assertsession log} crée un log avec l'état de la session pour chaque instance du cache
//
// Utile pour optimiser avec plugin macrosession et vérifier que ça se passe bien, et durablement, comme prévu
//
function cachelab_filtre_assertsession ($cache, $arg) {
	if (!is_array($cache) or !isset($cache['source']) or !isset($cache['lastmodified']) or !isset($cache['invalideurs'])) {
		spip_log ("cachelab_filtre_assertsession ne reçoit pas un cache mais".print_r($cache,1), "cachelab_assert");
		return null;
	}
	$source = $cache['source']; 
	$source_file = str_replace(array('/','.'), '_', $source);
	$arg=trim($arg);
	
	$invalideurs = $cache['invalideurs'];

	// on teste l'invalideur session
	// On pourrait aussi tester par le nom du cache avec '/_([0-9a-f]{8}|)$/i' ?

	switch ($arg) {
	case 'login' :
		$ok = !empty($invalideurs['session']); // def et non vide
		break;
	case 'anonyme' :
		$ok = empty($invalideurs['session']);	// undef ou vide
		break;
	case 'oui' :
		$ok = isset($invalideurs['session']);	// défini, mais peut être vide
		break;
	case 'non' :
		$ok = !isset($invalideurs['session']);	// non défini
		break;
	case 'log' :
		if (!isset($invalideurs['session']))
			$log = 'non';
		elseif ($invalideurs['session'])
			$log = 'oui_login';
		else
			$log = 'oui_anonyme';
		$ok = true;
		spip_log ("session ? $log", "cachelab_".$source_file);
		break;
	default:
		$ok = false;
		$arg .= " : valeur incorrecte";
		break;
	}
	if (!$ok)
		spip_log ("$source : assertsession n'est pas $arg. invalideurs=".print_r($invalideurs,1), "cachelab_assertsession");
}
