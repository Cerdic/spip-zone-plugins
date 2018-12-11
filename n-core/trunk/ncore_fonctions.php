<?php
/**
 * Ce fichier contient les filtres de compilation des noisettes appelés par la balise #COMPILER_NOISETTE.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// --------------------- FILTRES TYPES DE NOISETTE -----------------------
// -----------------------------------------------------------------------

/**
 * Détermine si le type de noisette spécifié doit être inclus en AJAX ou pas. Cette fonction gère un cache
 * des indicateurs ajax.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses cache_lire()
 * @uses cache_ecrire()
 * @uses ncore_type_noisette_lister()
 * @uses ncore_type_noisette_initialiser_ajax()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$type_noisette
 * 	      Identifiant du type de noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 * 		`true` si la noisette doit être ajaxée, `false` sinon.
 */
function type_noisette_ajaxifier($plugin, $type_noisette, $stockage = '') {

	// On indexe le tableau des indicateurs ajax par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $est_ajax = array();

	if (!isset($est_ajax[$plugin][$type_noisette])) {
		include_spip('inc/ncore_cache');
		// On vérifie si on doit recalculer le cache ou pas.
		if ((_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))
		or (!$est_ajax[$plugin] = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_AJAX))) {
			// On charge l'API de N-Core.
			include_spip("ncore/ncore");

			// On détermine la valeur par défaut de l'ajax des noisettes pour le plugin appelant.
			$defaut_ajax = ncore_type_noisette_initialiser_ajax($plugin);

			// On répertorie la configuration ajax de toutes les noisettes disponibles et on compare
			// avec la valeur par défaut configurée pour le service appelant.
			if ($ajax_types_noisette = ncore_type_noisette_lister($plugin,'ajax', $stockage)) {
				foreach ($ajax_types_noisette as $_type_noisette => $_ajax) {
					$est_ajax[$plugin][$_type_noisette] = ($_ajax == 'defaut')
						? $defaut_ajax
						: ($_ajax == 'non' ? false : true);
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant la valeur ajax par défaut afin de toujours renvoyer
			// quelque chose.
			if (!isset($est_ajax[$plugin][$type_noisette])) {
				$est_ajax[$plugin][$type_noisette] = $defaut_ajax;
			}

			// In fine, on met à jour le cache
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_AJAX, $est_ajax[$plugin]);
		}
	}

	return $est_ajax[$plugin][$type_noisette];
}

/**
 * Détermine si la noisette spécifiée doit être incluse dynamiquement ou pas. Cette fonction gère un cache
 * des indicateurs d'inclusion dynamique.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses cache_lire()
 * @uses cache_ecrire()
 * @uses ncore_type_noisette_lister()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 * 	      Identifiant du type de noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 * 		`true` si le type de noisette doit être inclus dynamiquement, `false` sinon.
 */
function type_noisette_dynamiser($plugin, $type_noisette, $stockage = '') {

	// On indexe le tableau des indicateurs ajax par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $est_dynamique = array();

	if (!isset($est_dynamique[$plugin][$type_noisette])) {
		include_spip('inc/ncore_cache');
		// On doit recalculer le cache ou pas.
		if ((_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))
		or (!$est_dynamique[$plugin] = cache_lire($type_noisette, _NCORE_NOMCACHE_TYPE_NOISETTE_INCLUSION))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On répertorie la configuration d'inclusion de toutes le types noisettes disponibles et on
			// détermine si le type demandé est dynamique ou pas.
			if ($inclusion_types_noisette = ncore_type_noisette_lister($plugin,'inclusion', $stockage)) {
				foreach ($inclusion_types_noisette as $_type_noisette => $_inclusion) {
					$est_dynamique[$plugin][$_type_noisette] = ($_inclusion == 'dynamique') ? true : false;
				}
			}

			// On vérifie que le type de noisette demandé est bien dans la liste.
			// Si non, on le rajoute en utilisant en positionnant l'inclusion dynamique à false ce qui est le défaut.
			if (!isset($est_dynamique[$plugin][$type_noisette])) {
				$est_dynamique[$plugin][$type_noisette] = false;
			}

			// In fine, on met à jour le cache
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_INCLUSION, $est_dynamique[$plugin]);
		}
	}

	return $est_dynamique[$plugin][$type_noisette];
}

/**
 * Renvoie le dossier relatif des types de noisette pour le plugin appelant ou la localisation
 * du type de noisette demandé.
 * Cette fonction gère le cas particulier de la noisette conteneur fournie par N-Core qui est elle
 * toujours dans le dossier par défaut de N-Core.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses ncore_type_noisette_initialiser_dossier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 * 	      Identifiant du type de noisette ou chaine vide si on ne veut que le dossier.
 *
 * @return string
 *        Chemin relatif du dossier où chercher les types de noisette ou du type de noisette demandé.
 */
function type_noisette_localiser($plugin, $type_noisette = '') {

	// Si le type de noisette est précisé et correspond à la noisette conteneur fournie par N-Core
	// alors on impose le dossier à celui par défaut de N-Core.
	// Sinon on prend le dossier du plugin appelant.
	include_spip('ncore/ncore');
	if ($type_noisette == 'conteneur') {
		$dossier = ncore_type_noisette_initialiser_dossier('ncore');
	} else {
		$dossier = ncore_type_noisette_initialiser_dossier($plugin);
	}

	// Si le type de noisette est vide on ne renvoie que le dossier, sinon on renvoie le chemin de
	// la noisette.
	return $dossier . $type_noisette;
}


// -----------------------------------------------------------------------
// -------------------- FILTRES & BALISES NOISETTES ----------------------
// -----------------------------------------------------------------------
include_spip('public/noisette_compiler');
include_spip('public/noisette_preview');
include_spip('public/noisette_repertorier');

/**
 * Renvoie le contexte de la noisette sous la forme d'un tableau éventuellement vide. Cette fonction gère un cache
 * des contextes génériques des types de noisette disponibles.
 *
 * @package SPIP\NCORE\NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses cache_lire()
 * @uses cache_ecrire()
 * @uses ncore_type_noisette_lister()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Tableau des identifiants de la noisette qui peut prendre la forme d'un tableau avec pour index
 *        id_noisette, id conteneur et rang_noisette, ce qui permet d'utiliser l'un ou l'autre des identifiants.
 * @param string $type_noisette
 * 	      Identifiant du type de noisette.
 * @param array  $environnement
 * 	      Tableau de l'environnement reçu par la noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return array
 * 		Le tableau éventuellement vide des éléments de contexte de la noisette.
 */
function noisette_contextualiser($plugin, $noisette, $type_noisette, $environnement, $stockage = '') {

	// Initialisation du tableau des contexte générique de chaque type de noisette.
	static $contextes_type_noisette = array();

	// On initialise le contexte de la noisette a minima.
	// -- on transmet toujours les identifiants de la noisette id_noisette et couple (id_conteneur, rang) qui sont
	//    fournis par la balise.
	$contexte = $noisette ? $noisette : array();

	// Récupération du contexte défini pour le type de noisette. Ce contexte est juste une liste de variables non
	// valorisées. La valorisation sera faite avec l'environnement.
	// -- les contextes sont stockés dans un cache dédié.
	if (!isset($contextes_type_noisette[$plugin][$type_noisette])) {
		// On vérifie si on doit recalculer le cache le cache ou pas.
		include_spip('inc/ncore_cache');
		if ((_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))
		or (!$contextes_type_noisette[$plugin] = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_CONTEXTE))) {
			// On répertorie la configuration du contexte de toutes les noisettes disponibles et on
			// le renvoie le résultat tel quel.
			include_spip('ncore/ncore');
			$contextes_type_noisette[$plugin] = ncore_type_noisette_lister($plugin, 'contexte', $stockage);

			// On vérifie que le type de noisette demandé est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant le contexte à tableau vide.
			if (!isset($contextes_type_noisette[$plugin][$type_noisette])) {
				$contextes_type_noisette[$plugin][$type_noisette] = serialize(array());
			}

			// In fine, on met à jour le cache
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_CONTEXTE, $contextes_type_noisette[$plugin]);
		}
	}
	// -- on inverse les index et valeurs du tableau de contexte pour obtenir un tableau similaire au contexte.
	$contexte_type_noisette = array_flip(unserialize($contextes_type_noisette[$plugin][$type_noisette]));

	// On construit le contexte final en fonction de celui du type de noisette.
	// On renvoie systématiquement le contexte minimal déjà initialisé et si le contexte du type de noisette contient:
	// - aucun => rien de plus.
	// - env ou vide => l'environnement complet également.
	// - une liste de variables => on renvoie également l'intersection de cette liste avec l'environnement.
	if (!isset($contexte_type_noisette['aucun'])) {
		if (isset($contexte_noisette['env'])) {
			$contexte = array_merge($environnement, $contexte);
		} else {
			$contexte = array_merge(array_intersect_key($environnement, $contexte_type_noisette), $contexte);
		}
	}

	return $contexte;
}


/**
 * Encapsule, si demandé, le contenu de la noisette issu de la compilation dans un HTML plus ou moins complexe.
 *
 * @package SPIP\NCORE\NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses ncore_noisette_initialiser_encapsulation()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $contenu
 *        Contenu compilé de la noisette en cours avant encapsulation.
 * @param string $encapsulation
 * 	      Indicateur d'encapsulation du contenu par un capsule ou pas.
 * @param string $css
 * 	      Styles à intégrer à la capsule.
 * @param mixed  $id_noisette
 * 	      Identifiant de la noisette.
 * @param string $type_noisette
 * 	      Identifiant du type de noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return string
 * 		Le contenu de la noisette encapsulé dans du HTML ou tel que fourni en entrée si pas d'encapsulation.
 */
function noisette_encapsuler($plugin, $contenu, $encapsulation, $css, $id_noisette, $type_noisette, $stockage = '') {

	// Initialisation du tableau du HTML des capsules indexé par plugin et nom de capsule.
	static $defaut_encapsulation = array();
	static $capsule_dist = array();

	// Détermination du défaut d'encapsulation.
	if (!isset($defaut_encapsulation[$plugin])) {
		$defaut_encapsulation[$plugin] = ncore_noisette_initialiser_encapsulation($plugin);
	}

	if (($encapsulation != 'non') or (($encapsulation == 'defaut') and ($defaut_encapsulation[$plugin]))) {
		// Détermination de la capsule à appliquer
		// On utilise soit la capsule propre à un type de noisette si elle existe, soit on utilise la capsule
		// par défaut qui porte le nom 'dist'.
		if (find_in_path("capsules/${type_noisette}.html")) {
			$nom_capsule = $type_noisette;
			$contexte_capsule = array('id_noisette' => $id_noisette, 'type_noisette' => $type_noisette, 'css' => $css);
		} else {
			$nom_capsule = 'dist';
			$contexte_capsule = array('type_noisette' => $type_noisette);
		}

		// Si on veut insérer la capsule dist (cas le plus fréquent), on accélère le processus en évitant de 
		// faire systématiquement un appel à recuperer_fond mais seulement une fois dans un même hit.
		// On met donc en variable statique la capsule dist compilée avec uniquement le type de noisette évalué, mais pas les styles
		// et on applique les styles avec un str_replace étant donné que l'on connait la structure de la capsule dist qui n'est jamais
		// modifiée ni surchargée.
		if ($nom_capsule == 'dist') {
			if (!isset($capsule_dist[$plugin])) {
				$capsule = recuperer_fond("capsules/${nom_capsule}", $contexte_capsule);
				$capsule_dist[$plugin] = $capsule;
			}
			$style_dist = $css ? " $css" : '';
			$capsule = str_replace('[ (#ENV{css})]', $style_dist, $capsule_dist[$plugin]);
		} else {
			$capsule = recuperer_fond("capsules/${nom_capsule}", $contexte_capsule);
		}

		// On insère le contenu de la noisette dans la capsule qui contient toujours une indication d'insertion explicite.
		$contenu = str_replace('<!--noisettes-->', $contenu, $capsule);
	}

	return $contenu;
}

 
// -----------------------------------------------------------------------
// ------------------------- BALISES CONTENEURS --------------------------
// -----------------------------------------------------------------------
include_spip('public/conteneur_identifier');
