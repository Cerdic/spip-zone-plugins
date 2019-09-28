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
		include_spip('ncore_fonctions');
		$squelette = isset($flux['args']['fond']) ? $flux['args']['fond'] : '';
		$dossier_squelette = dirname($squelette);
		if ($squelette
		and ($squelette != 'body')
		and ($squelette != 'structure')
		and ($dossier_squelette != trim(type_noisette_localiser('noizetier'), '/'))
		and ($dossier_squelette != trim(type_noisette_localiser('ncore'), '/'))) {
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
				$blocs = page_noizetier_lister_blocs($page);
				if (in_array($bloc, $blocs)) {
					// Traitement des cas particuliers de certaines compositions
					// TODO : cela sert à quoi ?
					$composition = isset($flux['args']['contexte']['composition'])
						? $flux['args']['contexte']['composition']
						: '';
					// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
					// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
					if ($composition != '' and page_noizetier_extraire_composition($page) == '' and page_noizetier_extraire_type($page) != 'page') {
						$page .= '-' . $composition;
					}

					// On détermine si on est en présence d'un objet ou d'une page (ou composition).
					// Attention même s'il s'agit d'un objet, il n'y a pas forcément des noisettes propres à celui-ci,
					// dans ce cas on se rabat sur les noisettes de la page.
					// -- recherche en priorité d'une correspondance d'objet précis
					// -- ajout de l'id_conteneur dans le contexte
					include_spip('inc/noizetier_conteneur');
					include_spip('inc/noizetier_objet');
					if (
						(isset($flux['args']['contexte']['type-page'])
						and ($objet = $flux['args']['contexte']['type-page'])
						and ($cle_objet = id_table_objet($objet))
						and (isset($flux['args']['contexte'][$cle_objet]))
						and ($id_objet = intval($flux['args']['contexte'][$cle_objet])))
						and $compteurs_noisette = objet_noizetier_compter_noisettes($objet, $id_objet)
					) {
						// C'est un objet.
						$est_objet = true;
						// -- identification de l'objet
						$contexte['objet'] = $objet;
						$contexte['id_objet'] = $id_objet;
						// -- identification du conteneur
						$contexte['id_conteneur'] = conteneur_noizetier_composer($contexte, $bloc);
						// -- identification du bloc et des compteurs de noisettes de chaque bloc de l'objet.
						$contexte['bloc'] = $bloc;
					} else {
						// C'est une page ou une composition.
						$est_objet = false;
						// -- identification du conteneur
						$contexte['id_conteneur'] = conteneur_noizetier_composer($page, $bloc);
						// -- identification du bloc et des compteurs de noisettes de chaque bloc de la page.
						$contexte['bloc'] = $bloc;
						$compteurs_noisette = page_noizetier_compter_noisettes($page);
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
						$fond_compilation_bloc = 'conteneur_compiler';
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
	// -- filtre sur l'indicateur est_page_objet à oui car on ne considère que les compositions virtuelles sur objet
	// -- filtre sur le type de contenu ou pas suivant l'appel
	include_spip('inc/noizetier_page');
	$informations = array('type', 'composition', 'nom', 'description', 'icon', 'branche');
	$filtres = array('est_virtuelle' => 'oui', 'est_page_objet' => 'oui');
	if ($type) {
		$filtres['type'] = $type;
	}
	$compositions_virtuelles = page_noizetier_repertorier($informations, $filtres);

	if ($compositions_virtuelles) {
		// On insère les compositions virtuelles selon le format imposé par le plugin Compositions
		foreach ($compositions_virtuelles as $_configuration) {
			if ($informer){
				$flux['data'][$_configuration['type']][$_configuration['composition']] = array(
					'nom' 			=> typo($_configuration['nom']),
					'description'	=> isset($_configuration['description']) ? typo($_configuration['description']) : '',
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
 * Pipeline styliser pour les compositions virtuelles du noizetier.
 *
 * @param array $flux
 *
 * @return array
 */
function noizetier_styliser($flux) {
/*
	// Initialisation du squelette, du fond et de l'extension.
	$squelette = $flux['data'];
	$fond = $flux['args']['fond'];
	$ext = $flux['args']['ext'];

	// Si le squelette est vide, il est probable que l'on soit en présence d'une composition virtuelle.
	if (!$squelette) {
		include_spip('inc/noizetier_page');
	    if ($page = page_noizetier_lire($fond, array('est_virtuelle', 'est_page_objet'))
	    and ($page['est_virtuelle'] == 'oui')
	    and ($page['est_page_objet'] == 'oui')) {
			// Composition virtuelle du noiZetier basée sur une page autonome.
			$flux['data'] = substr(find_in_path("page.$ext"), 0, -strlen(".$ext"));
			$flux['args']['composition'] = $fond;
	    }
	}
*/
	return $flux;
}

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
//	if (autoriser('configurer', 'noizetier')) {
//		$bouton = recuperer_fond('prive/squelettes/inclure/inc-bouton_voir_noisettes');
//		$flux['data'] = preg_replace('%(<!--extra-->)%is', $bouton.'$1', $flux['data']);
//	}

	return $flux;
}


/**
 * Ajouter du contenu au centre de la page sur les pages privées
 *
 * Page d'adminisration des plugin : on supprime le cache des noisettes.
 * C'est un peu grossier mais pas trouvé de pipeline pour agir à la mise à jour d'un plugin.
 * Au moins, le cache est supprimé à chaque changement, mise à jour des plugins.
 *
 * Page d'un objet : inclure le squelette qui affiche un lien pour configurer les noisettes.
 *
 * @param $flux
 * @return mixed
 */
function noizetier_affiche_milieu($flux) {

	if (isset($flux['args']['exec'])) {
		// Initialisation de la page du privé
		$exec = $flux['args']['exec'];

		if ($exec == 'admin_plugin') {
			// Administration des plugins
			// On recharge les pages du noiZetier dont la liste ou l'activité a pu changer. Inutile de forcer un
			// rechargement complet.
			include_spip('inc/noizetier_page');
			page_noizetier_charger();
			// On recharge les types de noisettes dont la liste ou l'activité a pu changer. Inutile de forcer un
			// rechargement complet.
			include_spip('inc/ncore_type_noisette');
			type_noisette_charger('noizetier');

			// Suppression des caches N-Core nécessaires à la compilation des noisettes
			type_noisette_decacher('noizetier');

		} elseif (
			($objet_exec = trouver_objet_exec($exec))
			and !$objet_exec['edition']
			and include_spip('inc/autoriser')
			and autoriser('configurerpage', 'noizetier', 0, '', array('page' => $flux['args']['exec']))
		) {

			// Page d'un objet
			$cle_objet = $objet_exec['id_table_objet'];
			$objet     = $objet_exec['type'];
			$id_objet  = $flux['args'][$cle_objet];

			// Identifier la page et la composition
			$composition = '';
			if (test_plugin_actif('compositions')) {
				include_spip('inc/compositions');
				$composition = compositions_determiner($objet, $id_objet);
			};
			$page = $composition ? "$objet-$composition" : $objet;

			$contexte = array(
				'objet'       => $objet,
				'id_objet'    => $id_objet,
				'page'        => $page,
				'composition' => $composition,
			);
			if ($texte = recuperer_fond('prive/squelettes/inclure/inc-noisettes_objet', $contexte)) {
				if ($pos = strpos($flux['data'],'<!--affiche_milieu-->')) {
					$flux['data'] = substr_replace($flux['data'], $texte, $pos, 0);
				} else {
					$flux['data'] .= $texte;
				}
			}

		}
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
