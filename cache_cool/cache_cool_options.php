<?php
/*
 * Plugin Cache Cool
 * (c) 2009 Cedric
 * Distribue sous licence GPL
 *
 */

/**
 * Fonction chargee de produire le cache pour un contexte et un fond donne
 * et de le memoriser si besoin
 * 
 * S'insere a la place de la fonction du core public_produire_page_dist pour
 * decider si le cache existant peut etre servi froid, et lancer dans la queue
 * une tache de mise a jour du cache en cron
 *
 * Dans ce cas, on reentre ici avec $use_cache.
 * On verifie que le contexte produit un $use_cache et un $chemin_cache credibles
 * (si on est dans l'espace prive au moment du calcul differe, aucun cache ne sera demande)
 * Il faudrait idealement verifier qu'on retrouve le meme $chemin_cache
 * mais cela necessiterait d'avoir le $page et le $contexte qui utilises pour cela
 * dans le cas de la page principal dans public/assembler, mais qui ne sont pas fournis ici
 * 
 * Si le contexte est semblable, on lance le calcul et la mise a jour du cache, 
 * sinon on reprogramme avec les memes arguments
 * 
 * @param string $fond
 * @param array $contexte
 * @param int $use_cache
 * @param string $chemin_cache
 * @param array $contexte_cache
 * @param array $page
 * @param int $lastinclude
 * @param string $connect
 * @return array 
 */
function public_produire_page($fond, $contexte, $use_cache, $chemin_cache, $contexte_cache, $page, &$lastinclude, $connect='', $global_context=null, $init_time = null){
	static $processing = false;
	$background = false;

	// calcul differe du cache ?
	// prend la main si
	// - c'est un calcul normal avec mise en cache
	// - un cache existe deja qui peut etre servi
	// - c'est une visite anonyme (cache mutualise)
	// - on est pas deja en train de traiter un calcul en background
	if ($use_cache==1 AND $chemin_cache
		AND is_array($page) AND isset($page['texte'])
		AND !$GLOBALS['visiteur_session']['id_auteur']
		AND !$processing
		) {
		// si c'est un bot, on ne lance pas un calcul differe
		// ca ne sert qu'a remplir la queue qui ne sera pas videe par le bot (pas de cron)
		// mais on lui sert le cache froid tout de meme
		if (!defined('_IS_BOT') OR !_IS_BOT){
			// on differe la maj du cache et on affiche le contenu du cache ce coup ci encore
			$where = is_null($contexte_cache)?"principal":"inclure_page";
			// on reprogramme avec un $use_cache=2 qui permettra de reconnaitre ces calculs
			job_queue_add('public_produire_page',$c="Calcul du cache $fond [$where]",array($fond, $contexte, 2, $chemin_cache, $contexte_cache, array('contexte_implicite'=>$page['contexte_implicite']), $lastinclude, $connect, cache_cool_get_global_context(), $_SERVER['REQUEST_TIME']),"",TRUE);
		}
		gunzip_page($page); // decomprimer la page si besoin
		#spip_log($c,'cachedelai');
		return $page;
	}

	// si c'est un cacul differe, verifier qu'on est dans le bon contexte
	if ($use_cache==2){
		$cacher = charger_fonction('cacher','public');
		$cacher($contexte_cache, $use_cache, $chemin2, $page, $lastmodified);
		if (intval($use_cache)!==1 OR !$chemin2){
			@define('_CACHE_COOL_ABORT_DELAI',600);
			if (
				($use_cache!=0) // le cache a deja ete mis a jour !
				AND ($elapsed = time()-$init_time)<_CACHE_COOL_ABORT_DELAI // cette demande est moisie
				){
				// on n'est pas dans le bon contexte, il faut se reprogrammer !
				$where = is_null($contexte_cache)?"principal":"inclure_page";
				$args = func_get_args();
				job_queue_add('public_produire_page',$c="[Re$elapsed] Calcul du cache $fond [$where]",$args,"",TRUE);
				#spip_log($c,'cachedelai');
			}
			return;
		}
		if (!$processing)
			$processing = $background = true;
	}

	// positionner le contexte des globales si necessaire
	if (!is_null($global_context))
		cache_cool_global_context($global_context);
	include_spip('public/assembler');
	$page = public_produire_page_dist($fond, $contexte, $use_cache, $chemin_cache, $contexte_cache, $page, $lastinclude, $connect);
	// restaurer le contexte des globales si necessaire
	if (!is_null($global_context))
		cache_cool_global_context(false);

	if ($background) $processing = false;

	return $page;
}

$GLOBALS['spip_pipeline']['insert_head'] = str_replace('|f_jQuery','|cache_cool_f_jQuery',$GLOBALS['spip_pipeline']['insert_head']);

// Inserer jQuery sans test de doublon
// incompatible avec le calcul multiple de squelettes sur un meme hit
// http://doc.spip.org/@f_jQuery
function cache_cool_f_jQuery ($texte) {
	$x = '';
	foreach (pipeline('jquery_plugins',
	array(
		'javascript/jquery.js',
		'javascript/jquery.form.js',
		'javascript/ajaxCallback.js'
	)) as $script)
		if ($script = find_in_path($script))
			$x .= "\n<script src=\"$script\" type=\"text/javascript\"></script>\n";
	$texte = $x.$texte;
	
	return $texte;
}

/**
 * Definir un nouveau contexte de globales (en sauvegardant l'ancien),
 * ou restaurer l'ancien contexte avec la valeur false
 * @staticvar array $pile
 * @param array/bool $push
 */
function cache_cool_global_context($push){
	static $pile = array();
	// restaurer le contexte
	if ($push===false AND count($pile)) {
		cache_cool_set_global_contexte(array_shift($pile));
	}
	// definir un nouveau contexte
	else {
		// on empile le contexte actuel
		array_unshift($pile, cache_cool_get_global_context());
		// et on le modifie
		cache_cool_set_global_contexte($push);
	}
}

/**
 * Lire les globales utilisees implicitement dans le calcul des
 * squelettes, et retourner un tableau les contenant
 *
 * @return array
 */
function cache_cool_get_global_context(){
	$contexte = array();
	foreach(array(
		'spip_lang',
		'visiteur_session',
		'auteur_session',
		'marqueur',
		'dossier_squelettes',
		'_COOKIE',
		'_SERVER',
		'_GET',
		'_REQUEST',
		'profondeur_url',
		'REQUEST_URI',
		'REQUEST_METHOD',
	) as $v)
		$contexte[$v] = $GLOBALS[$v];
	$contexte['url_de_base'] = url_de_base(false);
	$contexte['nettoyer_uri'] = nettoyer_uri();
	return $contexte;
}

/**
 * Assigner les globales fournies par $c
 * @param array $c
 * @return void
 */
function cache_cool_set_global_contexte($c){
	if (!is_array($c)) return; // ne rien faire
	url_de_base($c['url_de_base']); unset($c['url_de_base']);
	nettoyer_uri($c['nettoyer_uri']); unset($c['nettoyer_uri']);
	foreach($c as $k=>$v){
		$GLOBALS[$k] = $v;
	}
	foreach(array(
		'HTTP_SERVER_VARS'=>'_SERVER',
		'HTTP_GET_VARS'=>'_GET',
		'HTTP_COOKIE_VARS'=>'_COOKIE',
		) as $k1=>$k2){
		$GLOBALS[$k1] = $GLOBALS[$k2];
	}
}
?>