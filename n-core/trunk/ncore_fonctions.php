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
 *        Identifiant du service de stockage à utiliser si précisé.
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
 *        Identifiant du service de stockage à utiliser si précisé.
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
			if ($inclusion_types_noisette = ncore_type_noisette_lister($plugin, 'inclusion', $stockage)) {
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
 * toujours dans le dossier par défaut de N-Core et n'est donc pas surchargeable.
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
 * des contextes non valorisés des types de noisette disponibles.
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
 *        id_noisette, id conteneur et rang_noisette, ce qui permet d'utiliser l'un ou l'autre des identifiants
 *        de la noisette.
 * @param string $type_noisette
 * 	      Identifiant du type de noisette.
 * @param array  $environnement
 * 	      Tableau de l'environnement reçu par la noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
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
 * Encapsule, si demandé, le contenu compile d'une ou d'un ensemble de noisettes dans un balisage HTML
 * plus ou moins complexe appelé une capsule.
 * Une noisette conteneur est considérée comme une capsule et donc traitée en tant que tel.
 *
 * @package SPIP\NCORE\NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses ncore_noisette_initialiser_encapsulation()
 * @uses type_noisette_localiser()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $contenu
 *        Contenu compilé à encapsuler.
 * @param string $encapsulation
 * 	      Indicateur d'encapsulation du contenu par un capsule ou par une noisette conteneur. Prend les valeurs
 *        `oui`, `non`, `defaut` pour une capsule et `conteneur` pour une noisette conteneur.
 * @param string $parametres
 *        Liste des paramètres de l'encapsulation. Pour une capsule, les index sont limités à `type_noisette`,
 *        `id_noisette` et `css`. Pour une noisette conteneur cette liste correspond au champ `parametres` de la
 *        noisette et à son type.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return string
 * 		Le contenu fourni encapsulé dans un balisage HTML ou tel que fourni en entrée si pas d'encapsulation.
 */
function noisette_encapsuler($plugin, $contenu, $encapsulation, $parametres, $stockage = '') {

	// Initialisation du tableau du HTML des capsules indexé par plugin et nom de capsule.
	static $defaut_encapsulation = array();

	// Détermination du défaut d'encapsulation.
	if (!isset($defaut_encapsulation[$plugin])) {
		$defaut_encapsulation[$plugin] = ncore_noisette_initialiser_encapsulation($plugin);
	}

	// Une noisette conteneur peut être assimilée à une capsule qui englobe non pas une noisette mais un ensemble
	// de noisettes. A ce titre, une noisette conteneur n'a jamais de capsule car elle est déjà une capsule.
	if (($encapsulation == 'oui') 
	or ($encapsulation == 'conteneur') 
	or (($encapsulation == 'defaut') and ($defaut_encapsulation[$plugin]))) {
		// Détermination de la capsule à appliquer
		if ($encapsulation == 'conteneur') {
			// Noisette conteneur:
			// La capsule est la noisette elle-même.
			$fond_capsule = type_noisette_localiser($plugin, $parametres['type_noisette']);
		} else {
			// Capsule de noisette:
			// On utilise soit la capsule propre au type de noisette si elle existe,
			// soit on utilise la capsule générique pour toute noisette qui porte le nom 'dist',
			// soit on utilise une pseudo-capsule qui englobe la noisette dans un div.
			if (find_in_path("capsules/{$parametres['type_noisette']}.html")) {
				$fond_capsule = "capsules/{$parametres['type_noisette']}";
			} elseif (find_in_path('capsules/dist.html')) {
				$fond_capsule = 'capsules/dist';
			} else {
				$fond_capsule = '';
			}
		}

		// Si on veut insérer la pseudo-capsule (cas le plus fréquent), on accélère le processus en évitant de 
		// faire systématiquement un appel à recuperer_fond(): on construit le HTML.
		// De fait, le fichier HTML de la pseudo-capsule n'existe pas et n'est donc pas surchargeable.
		if (!$fond_capsule) {
			$capsule =
'<div class="noisette noisette_' . $parametres['type_noisette'] . ($parametres['css'] ? " {$parametres['css']}" : '') . '">
	<!--noisettes-->
</div>';
		} else {
			$capsule = recuperer_fond($fond_capsule, $parametres);
		}

		// On insère le contenu de la noisette dans la capsule ou la noisette conteneur qui contient toujours
		// une indication d'insertion explicite.
		$contenu = str_replace('<!--noisettes-->', $contenu, $capsule);
	}

	return $contenu;
}


/**
 * Renvoie une liste de descriptions de noisettes appartenant à un conteneur donné ou pas et éventuellement filtrée
 * sur certains champs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang de la
 * noisette.
 *
 * @package SPIP\NCORE\NOISETTE\API
 *
 * @api
 * @filtre
 *
 * @uses ncore_noisette_lister()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur ou vide si on souhaite adresser tous les
 *        conteneurs.
 * @param string $cle
 *        Champ de la description d'une noisette servant d'index du tableau. On utilisera soit `id_noisette`
 *        soit `rang_noisette` (défaut).
 * @param array  $filtres
 *        Tableau associatif `[champ] = valeur` de critères de filtres sur les descriptions de types de noisette.
 *        Le seul opérateur possible est l'égalité.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau des descriptions des noisettes du conteneur indexé par le champ fourni en argument (par défaut le
 *        rang).
 */
function noisette_repertorier($plugin, $conteneur = array(), $cle = 'rang_noisette', $filtres = array(), $stockage = '') {

	// On indexe le tableau des noisettes par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents et aussi par la clé d'indexation.
	static $noisettes = array();

	if (!isset($noisettes[$plugin][$cle])) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On récupère la description complète de toutes les noisettes ou des noisettes appartenant au conteneur
		// spécifié.
		$noisettes[$plugin][$cle] = ncore_noisette_lister($plugin, $conteneur, '', $cle, $stockage);
	}

	// Application des filtres éventuellement demandés en argument de la fonction
	$noisettes_filtrees = $noisettes[$plugin][$cle];
	if ($filtres) {
		foreach ($noisettes_filtrees as $_noisette => $_description) {
			foreach ($filtres as $_critere => $_valeur) {
				if (isset($_description[$_critere]) and ($_description[$_critere] != $_valeur)) {
					unset($noisettes_filtrees[$_noisette]);
					break;
				}
			}
		}
	}

	return $noisettes_filtrees;
}

 
// -----------------------------------------------------------------------
// --------------------- FILTRES & BALISES CONTENEURS --------------------
// -----------------------------------------------------------------------
include_spip('public/conteneur_identifier');

/**
 * Calcule l'identifiant unique pour le conteneur sous forme de chaine.
 * Cette fonction est juste un wrapper pour le service ncore_conteneur_identifier().
 * Elle est utilisée par les balises #NOISETTE_COMPILER et #CONTENEUR_IDENTIFIER
 *
 * @package SPIP\NCORE\CONTENEUR\API
 *
 * @api
 * @filtre
 *
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return string
 *        Identifiant du conteneur ou chaine vide en cas d'erreur.
 */
function conteneur_identifier($plugin, $conteneur, $stockage = '') {

	include_spip('ncore/ncore');
	$identifiant = ncore_conteneur_identifier($plugin, $conteneur, $stockage);

	return $identifiant;
}
