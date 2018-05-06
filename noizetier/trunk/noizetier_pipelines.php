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

	// Par défaut le noiZetier intercepte le pipeline recuperer_fond pour y insérer les noisettes configurées
	// pour la page en cours de traitement.
	// Il est cependant possible de désactiver ce fonctionnement en positionnant la constante _NOIZETIER_RECUPERER_FOND
	// à false.
	if ((defined('_NOIZETIER_RECUPERER_FOND') ? _NOIZETIER_RECUPERER_FOND : true) and !test_espace_prive()) {
		include_spip('inc/noizetier_page');
		$fond = isset($flux['args']['fond']) ? $flux['args']['fond'] : '';
		if ($fond) {
			// On détermine la page et le bloc à partir du fond qui est de la forme bloc/page.
			$elements = explode('/', $fond);
			if (!empty($elements[1])) {
				$bloc = $elements[0];
				$page = $elements[1];
			} else {
				$bloc = '';
				$page = $elements[0];
			}

			// Traitement des cas particuliers de certaines compositions
			$composition = isset($flux['args']['contexte']['composition']) ? $flux['args']['contexte']['composition'] : '';
			// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
			// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
			if ($composition != '' and noizetier_page_composition($page) == '' and noizetier_page_type($page) != 'page') {
				$fond .= '-'.$composition;
				$page .= '-'.$composition;
			}

			// Tester l'installation du noizetier pour éviter un message d'erreur à l'installation
			// TODO : vérifier que ce cas n'est plus possible si on est pas dans le privé
			if (isset($GLOBALS['meta']['noizetier_base_version'])) {
				if (isset($flux['args']['contexte']['voir'])
				and ($flux['args']['contexte']['voir'] == 'noisettes')
				and !function_exists('autoriser')) {
					include_spip('inc/autoriser');
				}     // si on utilise le formulaire dans le public

				// On cherche en priorité une correspondance d'objet précis !
				// Sinon on cherche pour le type de page ou la composition
				$par_objet = false;
				include_spip('inc/noizetier_bloc');
				if (
					(
						isset($flux['args']['contexte']['type-page'])
						and $objet = $flux['args']['contexte']['type-page']
						and $cle_objet = id_table_objet($objet)
						and isset($flux['args']['contexte'][$cle_objet])
						and $id_objet = intval($flux['args']['contexte'][$cle_objet])
						and $par_objet = array_key_exists($bloc, noizetier_bloc_compter_noisettes("${objet}-${id_objet}"))
					)
					or array_key_exists($bloc, noizetier_bloc_compter_noisettes($page))
				) {
					$contexte = $flux['data']['contexte'];
					$contexte['bloc'] = $bloc;

					include_spip('inc/noizetier_conteneur');
					if ($par_objet) {
						$contexte['objet'] = $objet;
						$contexte['id_objet'] = $id_objet;
						$contexte['id_conteneur'] = noizetier_conteneur_composer($contexte, $bloc);
					} else {
						$page = !empty($contexte['type-page']) ? $contexte['type-page'] : $contexte['page'];
						$page .= !empty($contexte['composition']) ? '-' . $contexte['composition'] : '';
						$contexte['id_conteneur'] = noizetier_conteneur_composer($page, $bloc);
					}

					if (isset($flux['args']['contexte']['voir']) && $flux['args']['contexte']['voir'] == 'noisettes' && autoriser('configurer', 'noizetier')) {
						$complements = recuperer_fond('bloc_preview', $contexte, array('raw' => true));
					} else {
						$complements = recuperer_fond('bloc_compiler', $contexte, array('raw' => true));
					}

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
				// Il faut ajouter les blocs vides en mode voir=noisettes
				elseif (
					isset($flux['args']['contexte']['voir'])
					and $flux['args']['contexte']['voir'] == 'noisettes'
					and autoriser('configurer', 'noizetier')
				) {
					$contexte = $flux['data']['contexte'];
					$contexte['bloc'] = $bloc;

					// Si ya au moins une noisette pour cet objet peu importe le bloc
					if (
						isset($flux['args']['contexte']['type-page'])
						and $objet = $flux['args']['contexte']['type-page']
						and sql_fetsel('id_noisette', 'spip_noisettes', array('objet = '.sql_quote($objet),'id_objet = '.$id_objet))
					) {
						$contexte['objet'] = $objet;
						$contexte['id_objet'] = $id_objet;
					}

					$page = isset($contexte['type']) ? $contexte['type'] : (isset($contexte['type-page']) ? $contexte['type-page'] : '');
					$page .= (isset($contexte['composition']) && $contexte['composition']) ? '-'.$contexte['composition'] : '';
					$blocs = noizetier_page_lister_blocs($page);
					if (isset($blocs[$bloc])) {
						$complements = recuperer_fond('bloc_preview', $contexte, array('raw' => true));
						$flux['data']['texte'] .= $complements['texte'];
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
					'icon' 			=> noizetier_icone_chemin($_configuration['icon']),
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
function noizetier_styliser($flux) {
	if (defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') and _NOIZETIER_COMPOSITIONS_TYPE_PAGE) {
		$squelette = $flux['data'];
		$fond = $flux['args']['fond'];
		$ext = $flux['args']['ext'];
		
		// Si on n'a pas trouvé de squelette
		if (!$squelette) {
			$noizetier_compositions = (isset($GLOBALS['meta']['noizetier_compositions'])) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
			// On vérifie qu'on n'a pas demandé une composition du noizetier de type page et qu'on appele ?page=type
			if (isset($noizetier_compositions['page'][$fond])) {
				$flux['data'] = substr(find_in_path("page.$ext"), 0, -strlen(".$ext"));
				$flux['args']['composition'] = $fond;
			}
		}
	}

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

// TODO : à supprimer ou transformer pour exclure certaines pages pour l'utilisateur admin et pas webmestre
function noizetier_noizetier_lister_pages($flux) {
	return $flux;
}
function noizetier_noizetier_blocs_defaut($flux) {
	return $flux;
}
function noizetier_noizetier_config_export($flux) {
	return $flux;
}
function noizetier_noizetier_config_import($flux) {
	return $flux;
}

// les boutons d'administration : ajouter le mode voir=noisettes
function noizetier_formulaire_admin($flux) {
	if (autoriser('configurer', 'noizetier')) {
		$bouton = recuperer_fond('prive/bouton/voir_noisettes');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', $bouton.'$1', $flux['data']);
	}

	return $flux;
}

// Lorsque l'on affiche la page admin_plugin, on supprime le cache des noisettes.
// C'est un peu grossier mais pas trouvé de pipeline pour agir à la mise à jour d'un plugin.
// Au moins, le cache est supprimé à chaque changement, mise à jour des plugins.

function noizetier_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];

	if ($exec == 'admin_plugin') {
		include_spip('inc/noizetier_page');
		noizetier_page_charger();
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
