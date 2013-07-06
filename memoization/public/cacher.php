<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/* compat SPIP 1.9 */
if(!function_exists('test_espace_prive')) {
	function test_espace_prive() {
		return !!_DIR_RACINE;
	}
}
/* compat SPIP 2.1 */
if(defined('_LOG_INFO_IMPORTANTE')) {
	function memoization_log($log) { spip_log($log, _LOG_INFO_IMPORTANTE); }
} else {
	function memoization_log($log) { spip_log($log); }
}

// http://doc.spip.org/@generer_nom_fichier_cache
function generer_nom_fichier_cache($contexte, $page) {
	// l'indicateur sert a savoir un peu de quoi il s'agit
	// quand on regarde dans le cache ; on le met a la fin
	// du nom pour que ca "melange" mieux sous memcache
	$indicateur = is_array($page)
		? $page['contexte_implicite']['cache'] # SPIP 2.1
		: strval($page); # SPIP 2.0 ou autre

	return
		md5(var_export(array($contexte, $page),true))
		. '-'.$indicateur;
}

// Parano : on signe le cache, afin d'interdire un hack d'injection
// dans notre memcache
function cache_signature(&$page) {
	if (!isset($GLOBALS['meta']['cache_signature'])){
		include_spip('inc/acces');
		include_spip('auth/sha256.inc');
		ecrire_meta('cache_signature', _nano_sha256($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SERVER_SIGNATURE"] . creer_uniqid()), 'non');
	}
	return crc32($GLOBALS['meta']['cache_signature'].$page['texte']);
}

/**
 * gestion des delais d'expiration du cache...
 * $page passee par reference pour accelerer
 * 
 * La fonction retourne 
 * 1 si il faut mettre le cache a jour
 * 0 si le cache est valide
 * -1 si il faut calculer sans stocker en cache
 *
 * @param array $page
 * @param int $date
 * @return -1/0/1
 */
/// http://doc.spip.org/@cache_valide
function cache_valide(&$page, $date) {
	$now = $_SERVER['REQUEST_TIME'];

	if (defined('_VAR_NOCACHE') AND _VAR_NOCACHE) return -1;
	if (isset($GLOBALS['meta']['cache_inhib']) AND $_SERVER['REQUEST_TIME'] AND $_SERVER['REQUEST_TIME']<$GLOBALS['meta']['cache_inhib']) return -1;
	if (isset($GLOBALS['var_nocache']) AND $GLOBALS['var_nocache']) return -1;
	if (defined('_NO_CACHE')) return (_NO_CACHE==0 AND !isset($page['texte']))?1:_NO_CACHE;

	// pas de cache ? on le met a jour, sauf pour les bots (on leur calcule la page sans mise en cache)
	if (!$page OR !isset($page['texte']) OR !isset($page['entetes']['X-Spip-Cache'])) return _IS_BOT?-1:1;

	// controle de la signature
	if ($page['sig'] !== cache_signature($page))
		return _IS_BOT?-1:1;

	// #CACHE{n,statique} => on n'invalide pas avec derniere_modif
	// cf. ecrire/public/balises.php, balise_CACHE_dist()
	if (!isset($page['entetes']['X-Spip-Statique']) OR $page['entetes']['X-Spip-Statique'] !== 'oui') {

		// Cache invalide par la meta 'derniere_modif'
		// sauf pour les bots, qui utilisent toujours le cache
		if (!_IS_BOT
		AND $GLOBALS['derniere_modif_invalide']
		AND $date < $GLOBALS['meta']['derniere_modif'])
			return 1;

		// Apparition d'un nouvel article post-date ?
		if ($GLOBALS['meta']['post_dates'] == 'non'
		AND isset($GLOBALS['meta']['date_prochain_postdate'])
		AND $now > $GLOBALS['meta']['date_prochain_postdate']) {
			spip_log('Un article post-date invalide le cache');
			include_spip('inc/rubriques');
			ecrire_meta('derniere_modif', $now);
			calculer_prochain_postdate();
			return 1;
		}

	}

	// Sinon comparer l'age du fichier a sa duree de cache
	$duree = intval($page['entetes']['X-Spip-Cache']);
	$cache_mark = (isset($GLOBALS['meta']['cache_mark'])?$GLOBALS['meta']['cache_mark']:0);
	if ($duree == 0)  #CACHE{0}
		return -1;
	// sauf pour les bots, qui utilisent toujours le cache
	else if ((!_IS_BOT AND $date + $duree < $now)
		# le cache est anterieur a la derniere purge : l'ignorer, meme pour les bots
	  OR $date<$cache_mark)
		return _IS_BOT?-1:1;
	else
		return 0;
}

// Creer le fichier cache
# Passage par reference de $page par souci d'economie
// http://doc.spip.org/@creer_cache
function creer_cache(&$page, &$chemin_cache, &$memo) {

	// Ne rien faire si on est en preview, debug, ou si une erreur
	// grave s'est presentee (compilation du squelette, MySQL, etc)
	// le cas var_nocache ne devrait jamais arriver ici (securite)
	// le cas spip_interdire_cache correspond a une ereur SQL grave non anticipable
	if ((defined('_VAR_NOCACHE') AND _VAR_NOCACHE)
		OR (isset($GLOBALS['var_nocache']) AND $GLOBALS['var_nocache']) // compat SPIP 2.x
		OR defined('spip_interdire_cache'))
		return;

	// Si la page c1234 a un invalideur de session 'zz', sauver dans
	// 'tmp/cache/MD5(chemin_cache)_zz'
	if (isset($page['invalideurs'])
	AND isset($page['invalideurs']['session'])) {
		// on verifie que le contenu du chemin cache indique seulement
		// "cache sessionne" ; sa date indique la date de validite
		// des caches sessionnes
		if (!is_array($tmp = $memo->get($chemin_cache))) {
			$tmp = array(
				'invalideurs' => array('session' => ''),
				'lastmodified' => $_SERVER['REQUEST_TIME']
			);
			$ok = $memo->set($chemin_cache, $tmp);
			memoization_log((_IS_BOT?"Bot:":"")."Creation du cache sessionne $chemin_cache ". $memo->methode ." pour "
				. $page['entetes']['X-Spip-Cache']." secondes". ($ok?'':' (erreur!)'));
		}
		$chemin_cache .= '_'.$page['invalideurs']['session'];
	}

	// ajouter la date de production dans le cache lui meme
	// (qui contient deja sa duree de validite)
	$page['lastmodified'] = $_SERVER['REQUEST_TIME'];

	// signer le contenu
	$page['sig']= cache_signature($page);

	// compresser si elle est > 16 ko
	if (strlen($page['texte']) > 16384
	AND function_exists('gzcompress')) {
		$page['texte'] = gzcompress($z = $page['texte']);
		$page['gz'] = true;
	}

	// memoizer...
	// on ajoute une heure histoire de pouvoir tourner
	// sur le cache quand la base de donnees est plantee (a tester)
	$ok = $memo->set($chemin_cache, $page, 3600+$page['entetes']['X-Spip-Cache']);

	// retablir le texte pour l'afficher
	if (isset($z)) {
		$page['texte'] = $z;
		unset($page['gz']);
	}

	memoization_log((_IS_BOT?"Bot:":"")."Creation du cache $chemin_cache ". $memo->methode ." pour "
		. $page['entetes']['X-Spip-Cache']." secondes". ($ok?'':' (erreur!)'));

	// Inserer ses invalideurs
	/* compat SPIP 1.9 : ne pas appeler les invalideurs du tout */
	if (!(isset($GLOBALS['spip_version']) AND $GLOBALS['spip_version']<2)) {
		include_spip('inc/invalideur');
		maj_invalideurs($chemin_cache, $page);
	}
}


// purger un petit cache (tidy ou recherche) qui ne doit pas contenir de
// vieux fichiers ; (cette fonction ne sert que dans des plugins obsoletes)
// http://doc.spip.org/@nettoyer_petit_cache
function nettoyer_petit_cache($prefix, $duree = 300) {
	// determiner le repertoire a purger : 'tmp/CACHE/rech/'
	$dircache = sous_repertoire(_DIR_CACHE,$prefix);
	if (spip_touch($dircache.'purger_'.$prefix, $duree, true)) {
		foreach (preg_files($dircache,'[.]txt$') as $f) {
			if ($_SERVER['REQUEST_TIME'] - (@file_exists($f)?@filemtime($f):0) > $duree)
				spip_unlink($f);
		}
	}
}


// Interface du gestionnaire de cache
// Si son 3e argument est non vide, elle passe la main a creer_cache
// Sinon, elle recoit un contexte (ou le construit a partir de REQUEST_URI)
// et affecte les 4 autres parametres recus par reference:
// - use_cache qui vaut
//     -1 s'il faut calculer la page sans la mettre en cache
//      0 si on peut utiliser un cache existant
//      1 s'il faut calculer la page et la mettre en cache
// - chemin_cache qui est le chemin d'acces au fichier ou vide si pas cachable
// - page qui est le tableau decrivant la page, si le cache la contenait
// - lastmodified qui vaut la date de derniere modif du fichier.
// Elle retourne '' si tout va bien
// un message d'erreur si le calcul de la page est totalement impossible

// http://doc.spip.org/@public_cacher_dist
function public_cacher($contexte, &$use_cache, &$chemin_cache, &$page, &$lastmodified) {
	$chemin_cache_session = false;
	
	/* compat SPIP 1.9 */
	if (is_null($contexte) AND function_exists('nettoyer_uri'))
		$contexte = array('uri' => nettoyer_uri());

	static $memo;
	if (!isset($memo)) {
		include_spip('inc/memoization');
		$cfg = @unserialize($GLOBALS['meta']['memoization']);
		$memo = new MCache((isset($cfg['pages']) AND $cfg['pages'])? $cfg['pages'] : $cfg['methode']);
	}

	/* compat SPIP 1.9 */
	if (is_array($page) AND !isset($page['entetes']['X-Spip-Cache']))
		$page['duree'] = $page['entetes']['X-Spip-Cache'] = isset($GLOBALS['delais']) ? $GLOBALS['delais'] : null;

	// Second appel, destine a l'enregistrement du cache sur le disque
	if (isset($chemin_cache)) return creer_cache($page, $chemin_cache, $memo);

	// Toute la suite correspond au premier appel
	$contexte_implicite = $page['contexte_implicite'];

	// Cas ignorant le cache car completement dynamique
	if ($_SERVER['REQUEST_METHOD'] == 'POST'
	OR (substr($contexte_implicite['cache'],0,8)=='modeles/') 
	OR (_request('connect'))
// Mode auteur authentifie appelant de ecrire/ : il ne faut rien lire du cache
// et n'y ecrire que la compilation des squelettes (pas les pages produites)
// car les references aux repertoires ne sont pas relatifs a l'espace public
	OR test_espace_prive()) {
		$use_cache = -1;
		$lastmodified = 0;
		$chemin_cache = "";
		$page = array();
		return;
	}

	// Controler l'existence d'un cache nous correspondant
	$chemin_cache = generer_nom_fichier_cache($contexte, $page);
	$lastmodified = 0;

	// charger le cache s'il existe
	if (!is_array($page = $memo->get($chemin_cache)))
		$page = array();

	// s'il est sessionne, charger celui correspondant a notre session
	if (isset($page['invalideurs'])
	AND isset($page['invalideurs']['session'])) {
		$chemin_cache_session = $chemin_cache . '_' . spip_session();
		if (is_array($page_session = $memo->get($chemin_cache_session))
		AND $page_session['lastmodified'] >= $page['lastmodified'])
			$page = $page_session;
		else
			$page = array();
	}

	// dezip si on l'a zipe
	if (isset($page['gz'])) {
		$page['texte'] = gzuncompress($page['texte']);
		unset($page['gz']);
	}

	if (intval($GLOBALS['spip_version_branche'])<3){
		// HEAD : cas sans jamais de calcul pour raisons de performance
		// supprime en SPIP 3 par http://core.spip.org/projects/spip/repository/revisions/19959
		if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
			$use_cache = 0;
			$page = array('contexte_implicite'=>$contexte_implicite);
			return;
		}
	}

	// Si un calcul, recalcul [ou preview, mais c'est recalcul] est demande,
	// on supprime le cache
	if (((isset($GLOBALS['var_mode']) && $GLOBALS['var_mode']) OR (defined('_VAR_MODE') && _VAR_MODE)) &&
		(isset($_COOKIE['spip_session'])
		|| isset($_COOKIE['spip_admin'])
		|| @file_exists(_ACCESS_FILE_NAME))
	) {
		$page = array('contexte_implicite'=>$contexte_implicite); // ignorer le cache deja lu
		include_spip('inc/invalideur');
		if (function_exists('retire_caches')) retire_caches($chemin_cache); # API invalideur inutile
		$memo->del($chemin_cache);
		if ($chemin_cache_session)
			$memo->del($chemin_cache_session);
	}

	// $delais par defaut (pour toutes les pages sans #CACHE{})
	if (!isset($GLOBALS['delais'])) {
		define('_DUREE_CACHE_DEFAUT', 24*3600);
		$GLOBALS['delais'] = _DUREE_CACHE_DEFAUT;
	}

	// determiner la validite de la page
	if ($page) {
		$use_cache = cache_valide($page, isset($page['lastmodified']) ? $page['lastmodified']:null);
		// le contexte implicite n'est pas stocke dans le cache, mais il y a equivalence
		// par le nom du cache. On le reinjecte donc ici pour utilisation eventuelle au calcul
		$page['contexte_implicite'] = $contexte_implicite;
		if (!$use_cache)
			return;
	} else {
		$page = array('contexte_implicite'=>$contexte_implicite);
		$use_cache = cache_valide($page,0); // fichier cache absent : provoque le calcul
	}

	// Si pas valide mais pas de connexion a la base, le garder quand meme
	if (!spip_connect()) {
		if (isset($page['texte'])) {
			$use_cache = 0;
		}
		else {
			spip_log("Erreur base de donnees, impossible utiliser "._MEMOIZE." $chemin_cache");
			include_spip('inc/minipres');
			return minipres(_T('info_travaux_titre'),  _T('titre_probleme_technique'));
		}
	}

	if ($use_cache < 0) $chemin_cache = '';
	return;
}

// Faut-il decompresser ce cache ?
// (passage par reference pour alleger)
// http://doc.spip.org/@gunzip_page
function gunzip_page(&$page) {
	if ($page['gz']) {
		$page['texte'] = gzuncompress($page['texte']);
		$page['gz'] = false; // ne pas gzuncompress deux fois une meme page
	}
}

?>