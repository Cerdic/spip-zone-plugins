<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Pipeline recuperer_fond pour ajouter les noisettes.
 *
 * @param array $flux
 *
 * @return array
 */
function noizetier_recuperer_fond($flux) {

	// Le noiZetier v3 est essentiellement dédié aux squelettes Z.
	// De fait, il intercepte systématiquement tout appel à la fonction recuperer_fond() à partir du moment où celui-ci
	// ne concerne pas le privé.
	// Le fonctionnement du pipeline consiste à détecter si le squelette concerné par l'appel à recuperer_fond() est
	// celui d'un bloc autorisé pour une page ou un objet donné.
	if (!test_espace_prive()
	and isset($flux['data']['contexte'])
	and ($contexte = $flux['data']['contexte'])
	and !isset($contexte['fond_compilation_noizetier'])) {
		// On récupère le squelette en cours de traitement et on identifie si celui-ci est un bloc autorisé d'une
		// page ou d'un objet.
		// On exclut le squelette structure.html et les noisettes elles-mêmes.
		include_spip('ncore/noizetier');
		include_spip('ncore_fonctions');
		$squelette = isset($flux['args']['fond']) ? $flux['args']['fond'] : '';
		if ($squelette
		and ($squelette != 'body')
		and ($squelette != 'structure')
		and (dirname($squelette) != trim(type_noisette_localiser('noizetier'), '/'))) {
			// On détermine la page et le bloc à partir du squelette qui, en Z, est toujours de la forme bloc/page
			// ou bloc/type_objet.
			$extension = pathinfo($squelette,  PATHINFO_EXTENSION);
			$squelette = explode('/', $squelette);

			// Si le squelette n'est pas de la forme bloc/page alors on n'est pas en présence d'un bloc configurable
			// avec des noisettes.
			// On élimine aussi les js et css qui sont les seuls à avoir une extension.
			if ((count($squelette) == 2) and (!$extension)) {
				$bloc = $squelette[0];
				$page = $squelette[1];

				// On vérifie que le bloc fait bien partie de la liste des blocs configurables de la page.
				include_spip('inc/noizetier_page');
				$blocs = noizetier_page_lister_blocs($page);
				if (in_array($bloc, $blocs)) {
					// Traitement des cas particuliers de certaines compositions
					// TODO : cela sert à quoi ?
					$composition = isset($flux['args']['contexte']['composition'])
						? $flux['args']['contexte']['composition']
						: '';
					// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
					// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
					if ($composition != '' and noizetier_page_extraire_composition($page) == '' and noizetier_page_extraire_type($page) != 'page') {
						$page .= '-'.$composition;
					}

					// On détermine si on est en présence d'un objet ou d'une page (ou composition).
					// -- recherche en priorité d'une correspondance d'objet précis
					// -- ajout de l'id_conteneur dans le contexte
					include_spip('inc/noizetier_conteneur');
					if ((isset($flux['args']['contexte']['type-page'])
						and ($objet = $flux['args']['contexte']['type-page'])
						and ($cle_objet = id_table_objet($objet))
						and (isset($flux['args']['contexte'][$cle_objet]))
						and ($id_objet = intval($flux['args']['contexte'][$cle_objet])))) {
						// C'est un objet.
						$est_objet = true;
						// -- identification de l'objet
						$contexte['objet'] = $objet;
						$contexte['id_objet'] = $id_objet;
						// -- identification du conteneur
						$contexte['id_conteneur'] = noizetier_conteneur_composer($contexte, $bloc);
						// -- identification du bloc et des compteurs de noisettes de chaque bloc de l'objet.
						$contexte['bloc'] = $bloc;
						include_spip('inc/noizetier_objet');
						$compteurs_noisette = noizetier_objet_compter_noisettes($objet, $id_objet);
					} else {
						// C'est une page ou une composition.
						$est_objet = false;
						// -- identification du conteneur
						$contexte['id_conteneur'] = noizetier_conteneur_composer($page, $bloc);
						// -- identification du bloc et des compteurs de noisettes de chaque bloc de la page.
						$contexte['bloc'] = $bloc;
						$compteurs_noisette = noizetier_page_compter_noisettes($page);
					}
					$bloc_avec_noisettes = array_key_exists($bloc, $compteurs_noisette);

					// Suivant le mode, affichage normal ou prévisualisation (voir=noisettes), on affiche ou pas les
					// blocs vides.
					$fond_compilation_bloc = '';
					if (isset($flux['args']['contexte']['voir']) and ($flux['args']['contexte']['voir'] == 'noisettes')) {
						// Mode de prévisualisation.
						// -- Étant donné que la prévisualisation permet de configurer aussi les noisettes dans les blocs
						//    de la page ou de l'objet affiché, on teste l'autorisation (identique à noizetier_page
						//    du privé).
						include_spip('inc/autoriser');
						if (autoriser('configurer', 'noizetier')
						and ($bloc_avec_noisettes
							or (!$bloc_avec_noisettes and !$est_objet)
							or (!$bloc_avec_noisettes and $est_objet and $compteurs_noisette))) {
							$fond_compilation_bloc = 'bloc_compiler_editer';
						}
					} elseif ($bloc_avec_noisettes) {
						// Mode normal d'afichage
						$fond_compilation_bloc = 'bloc_compiler';
					}

					// Si le fond n'est pas vide c'est qu'il faut bien compiler soit une liste de noisettes
					// soit un bloc vide et l'ajouter au flux.
					if ($fond_compilation_bloc) {
						// On passe un indicateur dans le contexte qui permet de sortir rapidement du pipeline
						// lors des appels successifs liées à cette compilation.
						$contexte['fond_compilation_noizetier'] = true;
						$complements = recuperer_fond($fond_compilation_bloc, $contexte, array('raw' => true));

						// S'il y a une indication d'insertion explicite
						if (strpos($flux['data']['texte'], '<!--noisettes-->') !== false) {
							$flux['data']['texte'] = preg_replace(
								'%(<!--noisettes-->)%is',
								"${complements['texte']}\n" . '$1',
								$flux['data']['texte']
							);
						}
						else {
							$flux['data']['texte'] .= $complements['texte'];
						}
					}
				}
			}
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline boite_infos : ajouter un lien pour configurer les noisettes de ce contenu précisément.
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function noizetier_boite_infos($flux){

	include_spip('inc/autoriser');
	$opt = array('objet' => $flux['args']['type'], 'id_objet' => intval($flux['args']['id']));
	if (autoriser('configurerpage', 'noizetier', 0, '', $opt)) {
		// On cherche le nombre de noisettes déjà configurées pour ce contenu.
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'objet = ' . sql_quote($flux['args']['type']),
			'id_objet = ' . intval($flux['args']['id']));
		$nbr_noisettes = sql_countsel('spip_noisettes', $where);
		if (!$nbr_noisettes) {
			$texte = _T('noizetier:noisettes_configurees_aucune');
		}
		elseif ($nbr_noisettes == 1) {
			$texte = _T('noizetier:noisettes_configurees_une');
		}
		else {
			$texte = _T('noizetier:noisettes_configurees_nb', array('nb'=>$nbr_noisettes));
		}

		// On construit le bouton avec l'url adéquate.
		include_spip('inc/presentation');
		$url = generer_url_ecrire('noizetier_page');
		$url = parametre_url($url, 'objet', $flux['args']['type']);
		$url = parametre_url($url, 'id_objet', $flux['args']['id']);
		$flux['data'] .= icone_horizontale($texte, $url, 'noisette', $fonction='', $dummy='', $javascript='');
	}
	
	return $flux;
}


/**
 * Pipeline compositions_lister_disponibles pour ajouter les compositions du noizetier.
 *
 * @param array $flux
 *
 * @return array
 */
function noizetier_compositions_lister_disponibles($flux) {

	// Initialisation des arguments du pipeline
	$type = $flux['args']['type'];
	$informer = $flux['args']['informer'];

	// Récupération des compositions virtuelles du noiZetier afin de les injecter dans le pipeline
	// étant donné qu'elles ne peuvent pas être détectées par Compositions car sans XML
	// -- filtre sur l'indicateur est_virtuelle qui n'est à oui que pour les compositions
	// -- filtre sur le type de contenu ou pas suivant l'appel
	$select = array('page', 'type', 'composition', 'nom', 'description', 'icon', 'branche');
	$where = array('est_virtuelle=' . sql_quote('oui'));
	if ($type) {
		$where[] = 'type=' . sql_quote($type);
	}
	$compositions_virtuelles = sql_allfetsel($select, 'spip_noizetier_pages', $where);

	if ($compositions_virtuelles) {
		// On réindexe le tableau entier par l'identifiant de la page
		$compositions_virtuelles = array_column($compositions_virtuelles, null, 'page');

		// On insère les compositions virtuelles selon le format imposé par le plugin Compositions
		foreach ($compositions_virtuelles as $_identifiant => $_configuration) {
			if ($informer){
				$flux['data'][$_configuration['type']][$_configuration['composition']] = array(
					'nom' 			=> _T_ou_typo($_configuration['nom']),
					'description'	=> isset($_configuration['description']) ? _T_ou_typo($_configuration['description']) : '',
					'icon' 			=> chemin_image($_configuration['icon']),
					'branche' 		=> unserialize($_configuration['branche']),
					'class' 		=> '',
					'configuration'	=> '',
					'image_exemple'	=> '',
				);
			} else {
				$flux['date'][$_configuration['type']][$_configuration['composition']] = 1;
			}
		}
	}

	return $flux;
}

/**
 * Pipeline styliser pour les compositions du noizetier de type page si celles-ci sont activées.
 *
 * @param array $flux
 *
 * @return array
 */
 // TODO : revoir l'utilité de ce code qui est mort à priori car on a toujours une page source pour une composition
//function noizetier_styliser($flux) {
//	if (defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') and _NOIZETIER_COMPOSITIONS_TYPE_PAGE) {
//		$squelette = $flux['data'];
//		$fond = $flux['args']['fond'];
//		$ext = $flux['args']['ext'];
//
//		// Si on n'a pas trouvé de squelette
//		if (!$squelette) {
//			$noizetier_compositions = (isset($GLOBALS['meta']['noizetier_compositions'])) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
//			// On vérifie qu'on n'a pas demandé une composition du noizetier de type page et qu'on appele ?page=type
//			if (isset($noizetier_compositions['page'][$fond])) {
//				$flux['data'] = substr(find_in_path("page.$ext"), 0, -strlen(".$ext"));
//				$flux['args']['composition'] = $fond;
//			}
//		}
//	}
//
//	return $flux;
//}

/**
 * Pipeline jqueryui_forcer pour demander au plugin l'insertion des scripts pour .sortable().
 *
 * @param array $plugins
 *
 * @return array
 */
function noizetier_jqueryui_forcer($plugins) {
	$plugins[] = 'jquery.ui.core';
	$plugins[] = 'jquery.ui.widget';
	$plugins[] = 'jquery.ui.mouse';
	$plugins[] = 'jquery.ui.sortable';
	$plugins[] = 'jquery.ui.droppable';
	$plugins[] = 'jquery.ui.draggable';
	$plugins[] = 'jquery.ui.accordion';

	return $plugins;
}

// TODO : à supprimer ou transformer pour exclure certaines pages pour l'utilisateur admin et pas webmestre
/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_noizetier_lister_pages($flux) {
	return $flux;
}

/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_noizetier_blocs_defaut($flux) {
	return $flux;
}

/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_noizetier_config_export($flux) {
	return $flux;
}

/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_noizetier_config_import($flux) {
	return $flux;
}

// les boutons d'administration : ajouter le mode voir=noisettes
/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_formulaire_admin($flux) {
	if (autoriser('configurer', 'noizetier')) {
		$bouton = recuperer_fond('prive/squelettes/inclure/inc-bouton_voir_noisettes');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', $bouton.'$1', $flux['data']);
	}

	return $flux;
}

// Lorsque l'on affiche la page admin_plugin, on supprime le cache des noisettes.
// C'est un peu grossier mais pas trouvé de pipeline pour agir à la mise à jour d'un plugin.
// Au moins, le cache est supprimé à chaque changement, mise à jour des plugins.

/**
 * @param $flux
 *
 * @return mixed
 */
function noizetier_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];

	if ($exec == 'admin_plugin') {
		// On recharge les pages du noiZetier dont la liste ou l'activité a pu changer. Inutile de forcer un
		// rechargement complet.
		include_spip('inc/noizetier_page');
		noizetier_page_charger();
		// On recharge les types de noisettes dont la liste ou l'activité a pu changer. Inutile de forcer un
		// rechargement complet.
		include_spip('inc/ncore_type_noisette');
		type_noisette_charger('noizetier');

		// Suppression des caches N-Core nécessaires à la compilation des noisettes
		include_spip('inc/ncore_cache');
		cache_supprimer('noizetier', _NCORE_NOMCACHE_TYPE_NOISETTE_CONTEXTE);
		cache_supprimer('noizetier', _NCORE_NOMCACHE_TYPE_NOISETTE_AJAX);
		cache_supprimer('noizetier', _NCORE_NOMCACHE_TYPE_NOISETTE_INCLUSION);
	}

	return $flux;
}



/**
 * Ajout de bulles de compagnon sur les pages de listing des pages et objets supportant des noisettes.
 *
 * @param array $flux
 * 		Données du pipeline
 *
 * @return array
 * 		Données du pipeline mises à jour
**/
function noizetier_compagnon_messages($flux) {

	$exec = $flux['args']['exec'];
	$pipeline = $flux['args']['pipeline'];
	$aides = &$flux['data'];

	switch ($pipeline) {
		case 'affiche_milieu':
			switch ($exec) {
				case 'noizetier_pages':
				case 'noizetier_objets':
					// Rappeler l'utilité du plugin Compositions si celui-ci n'est pas actif
					if (!defined('_DIR_PLUGIN_COMPOSITIONS')) {
						$aides[] = array(
							'id'      => 'composition',
							'titre'   => _T('noizetier:noizetier'),
							'texte'   => _T('noizetier:compositions_non_installe'),
							'statuts' => array('1comite', '0minirezo', 'webmestre')
						);
					}

					// Rappeler l'utilité du plugin IEconfig si celui-ci n'est pas actif
					if (!defined('_DIR_PLUGIN_IECONFIG')) {
						$aides[] = array(
							'id'      => 'ieconfig',
							'titre'   => _T('noizetier:noizetier'),
							'texte'   => _T('noizetier:ieconfig_non_installe'),
							'statuts' => array('1comite', '0minirezo', 'webmestre')
						);
					}
					break;
			}
			break;
	}

	return $flux;
}


/**
 * @param $boucle
 *
 * @return mixed
 */
function noizetier_pre_boucle($boucle) {

	if (!defined('_DIR_PLUGIN_COMPOSITIONS')) {
		// Si le plugin Compositions n'est pas actif, il faut exclure les compositions de la boucle NOIZETIER_PAGES.
		if ($boucle->type_requete == 'noizetier_pages'
		and empty($boucle->modificateur['tout'])) {
			// Pour exclure les compositions il faut insérer le critère {composition=''} systématiquement.
			$boucle->where[] = array("'='", "'noizetier_pages.composition'", "'\"\"'");
		}
	}

	return $boucle;
}


// Insertion des css du noiZetier pour l'édition avec le mode voir_noisettes.
/**
 * @param $flux
 *
 * @return string
 */
function noizetier_insert_head_css($flux) {
	static $done = false;
	if (!$done) {
		$done = true;
		if (_request('voir') == 'noisettes') {
			$flux .= '<link rel="stylesheet" href="' . find_in_path('css/noizetier.css') . '" type="text/css" media="all" />';
		}
	}

	return $flux;
}

/**
 * @param $flux
 *
 * @return string
 */
function noizetier_insert_head($flux) {
	// au cas ou il n'est pas implemente
	$flux .= noizetier_insert_head_css($flux);

	return $flux;
}
