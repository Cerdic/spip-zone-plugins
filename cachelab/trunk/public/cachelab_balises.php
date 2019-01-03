<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/cachelab_utils');

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
 * @examples
 *     ```
 *     #CACHE{24*3600}
 *     #CACHE{24*3600, cache-client}
 *     #CACHE{0} pas de cache
 *     ```
 * + Extensions par cachelab :
 *     ```
 *     #CACHE{3600,duree progressif}
 *     #CACHE{session assert non}
 *     #CACHE{24*3600, session}
 *     #CACHE{log contexte}
 *     #CACHE{log contexte/date_cration}
 *     #CACHE{log,session anonyme}
 *     ```
 * @note
 *   En absence de durée indiquée par cette balise, 
 *   la durée du cache est donnée
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
		$code = '';

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
				continue;
			}

			// il peut y avoir déjà eu, ou pas, du code
			$concat = (trim($code) ? '.' : '');

// ancienne syntaxe obsolète
			if (strpos($pa, 'duree-')===0) {
				$methode = substr($pa, 6);
				$ajout = "'<'.'" . '?php header("X-Spip-Methode-Duree-Cache: '.$methode.'"); ?' . "'.'>'";
				$code .= $concat.$ajout;
				spip_log ("#CACHE($pa) sur $sourcefile avec méthode de calcul de la durée du cache : $methode", 'cachelab_OBSOLETE');
			}

			if (strpos($pa, 'filtre-')===0) {
				$methode = substr($pa, 7); 
				$ajout = "'<'.'" . '?php header("X-Spip-Filtre-Cache: '.$methode.'"); ?' . "'.'>'";
				$code .= $concat.$ajout; 
				spip_log ("#CACHE($pa) sur $sourcefile avec filtre sur le cache complet : $methode", 'cachelab_OBSOLETE');
			}
// fin des syntaxes obsolètes

			list ($func, $args) = split_first_arg ($pa);
			switch ($func) {
			// TODO : également traiter ici les morceaux du core traités plus haut
			case 'statique' : 
			case 'duree' :
				$ajout = "'<'.'" . "?php header(\"X-Spip-Methode-Duree-Cache: $args\"); ?" . "'.'>'";
				$code .= $concat.$ajout;
				spip_log ("#CACHE{$pa} sur $sourcefile avec méthode de calcul de la durée du cache : $args", 'cachelab');
				break;
			
			case 'log' :
			case 'session' :
			case 'filtre' :
				$ajout = "'<'.'" . '?php header("X-Spip-Filtre-Cache: '.$pa.'"); ?' . "'.'>'";
				$code .= $concat.$ajout; 
				spip_log ("#CACHE{$pa} sur $sourcefile : filtre  $func($args) sur le cache complet", 'cachelab');
				break;
			default :
				break;
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
// usage : #CACHE{3600, duree progapprox} ou #CACHE{3600, duree-progapprox date_naissance}
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

/**
 * Calcule une durée de cache sans rafraîchissement jusqu'au lendemain minuit cinq.
 *
 * @param $date_unused : inutilisé
 * @return int : le nombre de secondes restant jusqu'au prochain minuit cinq
 */
function cachelab_duree_jusqueminuit($date_unused) {
    return strtotime('tomorrow') + 300 - time();
}

//
// Log tout ou un élément contenu par le tableau de cache
// dans un fichier de log dont le nom reprend le chemin du squelette
// (avec les / remplacés par des _)
//
// Exemples d'usages : 
//	#CACHE{3600,log} : log tout le cache, méta et html
//	#CACHE{log lastmodified}  : log l'entrée lastmodified du cache
// 	#CACHE{log contexte} : log tout le tableau d'environnement
//  #CACHE{log contexte/date_creation} : log l'entrée 'date_creation' de l'environnement
//
function cachelab_filtre_log($cache, $arg) {
	if (!is_array($cache) or !isset($cache['source']) or !isset($cache['lastmodified']) or !isset($cache['invalideurs'])) {
		spip_log ("cachelab_duree_progapprox ne reçoit pas un cache mais".print_r($cache,1), "cachelab_assert");
		return null;
	}
	$source_limace = slug_chemin($cache['source']);
	$arg=trim($arg);
	if ($arg) { 
		if (strpos($arg, '/')) {	#CACHE{log i/j}
			$ij=explode('/',$arg);
			$c = $cache[$i=trim(array_shift($ij))];
			$c = $c[trim($j=array_shift($ij))];
		}
		else {						#CACHE{log i}
			$c = $cache[$arg];
		}
	}
	else
		$c = $cache;				#CACHE{log}
	spip_log ("cache[$arg] : ".print_r($c,1), "cachelab_".$source_limace);
}


//
// Assertions sur le fait que le cache est sessionné ou non
// et que l'internaute est identifié ou non
//
// usages :
// 'assert' est utile pour vérifier que le sessionnement se passe bien comme prévu, et durablement,
//  et pour optimiser le découpage des noisettes et l'emploi de macrosession
// On indique l'état théorique du sessionnement.
// Les valeurs possibles sont : oui, oui_login, oui_anonyme, non, anonyme
// Dans le cas où un assert n'est pas vérifié, un log est créé dans le fichier cachelab_assertsession
//
// #CACHE{3600, session assert non} s'assure que les emplois sont non-sessionnés
// #CACHE{session assert oui} s'assure que tous les emplois sont sessionnés
// #CACHE{session assert oui_login} s'assure que tous les emplois sont sessionnés avec un internaute identifié
// #CACHE{session assert oui_anonyme} s'assure que tous les emplois sont sessionnés avec un internaute identifié (inutile ?)
// #CACHE{session assert anonyme} s'assure que tous les emplois sans internaute identifié
// 
// #CACHE{session log} loge l'état du sessionnement dans un cache dédié à ce squelette
// #CACHE{session insert} insère à la fin du cache l'affichage de l'état du sessionnement
// #CACHE{session echo} affiche l'état du sessionnement comme var_cache 
// mais pour ce cache seulement et seulement pour les caches dynamiques
//
function cachelab_filtre_session (&$cache, $totarg) {
	if (!is_array($cache) or !isset($cache['source']) or !isset($cache['lastmodified']) or !isset($cache['invalideurs'])) {
		spip_log ("cachelab_filtre_assertsession ne reçoit pas un cache mais".print_r($cache,1), "cachelab_assert");
		return null;
	}
	$source = $cache['source'];
	$source_limace = slug_chemin($source);
	list($func, $what) = split_first_arg($totarg);
	
	$invalideurs = $cache['invalideurs'];

	$sess = cachelab_etat_sessionnement($invalideurs, 'avec_details');
	$avec_echo = false;
	switch ($func) {
		case 'assert_echo' :
			$avec_echo = true;
		case 'assert' :
			switch($what) {
				case 'oui_login' :
				case 'oui_anonyme' :
				case 'non' :
					$ok = ($sess==$what);
					break;
				case 'anonyme' :
					$ok = empty($invalideurs['session']);	// oui_anonyme ou non
					break;
				case 'oui' :
					$ok = isset($invalideurs['session']);	// oui_anonyme ou oui_login
					break;
				default:
					spip_log ("Erreur de syntaxe : '$what' incorrect dans #CACHE{session $totarg}, il faut oui, oui_login, oui_anonyme, non ou anonyme", 'cachelab_erreur');
					break 2;
			}
			if (!$ok)  {
				spip_log ("$source : session n'est pas '$what'. invalideurs=".print_r($invalideurs,1), "cachelab_assertsession");
				if ($avec_echo) {
					echo "<div class='cachelab_blocs cachelab_assert'>
						<h6>Sessionnement $sess devrait être $what</h6>
						<small>Sessionnement incorrect pour $source</small>
						</div>";
				}
			}
			break;

	case 'insert' :
		global $Memoization;
		if (!isset($Memoization)) {
			spip_log ("Erreur dans $source : #CACHE{session insert} nécessite que le plugin Memoization soit activé", 'cachelab_erreur');
			echo "<div class='cachelab_blocs'><h6>Erreur dans $source : #CACHE{session insert} nécessite que le plugin Memoization soit activé</h6></div>";
			break;
		}
		$cache['texte'] .= '<'."?php echo '<div class=\"cachelab_blocs\"><h6>$source sessionné : $sess</h6></div>' ?>";
		$cache['process_ins'] = 'php';
		break;
	case 'echo' :
		echo "<div class='cachelab_blocs'><h6>$source sessionné : $sess</h6></div>";
		break;
	case 'log' :
		spip_log ('session : '.$sess, 'cachelab_session_'.$source_limace);
		break;
	default : 
		spip_log ("Syntaxe incorrecte dans $source : $func inconnu dans #CACHE{session $totarg}", 'cachelab_erreur');
		break;
	}
}

function cachelab_etat_sessionnement ($invalideurs, $detail=false) {
	if (!isset($invalideurs['session']))
		return 'non';
	if (!$detail)
		return 'oui';
	elseif ($invalideurs['session'])
		return 'oui_login';
	return 'oui_anonyme';
}
