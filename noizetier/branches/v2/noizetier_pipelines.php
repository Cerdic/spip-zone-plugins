<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function noizetier_header_prive($flux) {
	$css = direction_css(find_in_path('css/noizetier.css'));
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";

	return $flux;
}

/**
 * Pipeline recuperer_fond pour ajouter les noisettes.
 *
 * @param array $flux
 *
 * @return array
 */
function noizetier_recuperer_fond($flux) {
	if (defined('_NOIZETIER_RECUPERER_FOND') ? _NOIZETIER_RECUPERER_FOND : true) {
		include_spip('noizetier_fonctions');
		
		$fond = isset($flux['args']['fond']) ? $flux['args']['fond'] : '';
		$composition = isset($flux['args']['contexte']['composition']) ? $flux['args']['contexte']['composition'] : '';
		// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
		// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
		if ($composition != '' and noizetier_page_composition($fond) == '' and noizetier_page_type($fond) != 'page') {
			$fond .= '-'.$composition;
		}

		// Tester l'installation du noizetier pour éviter un message d'erreur à l'installation
		if (isset($GLOBALS['meta']['noizetier_base_version'])) {
			if (isset($flux['args']['contexte']['voir']) && $flux['args']['contexte']['voir'] == 'noisettes' && !function_exists('autoriser')) {
				include_spip('inc/autoriser');
			}     // si on utilise le formulaire dans le public
			
			// On cherche en priorité une correspondance d'objet précis !
			// Sinon on cherche pour le type de page ou la composition
			$par_objet = false;
			if (
				(
					isset($flux['args']['contexte']['type-page'])
					and $objet = $flux['args']['contexte']['type-page']
					and $cle_objet = id_table_objet($objet)
					and isset($flux['args']['contexte'][$cle_objet])
					and $id_objet = intval($flux['args']['contexte'][$cle_objet])
					and $par_objet = in_array($flux['args']['fond'], noizetier_lister_blocs_avec_noisettes_objet($objet, $id_objet))
				)
				or in_array($fond, noizetier_lister_blocs_avec_noisettes())
			) {
				$contexte = $flux['data']['contexte'];
				$contexte['bloc'] = substr($fond, 0, strpos($fond, '/'));
				
				if ($par_objet) {
					$contexte['objet'] = $objet;
					$contexte['id_objet'] = $id_objet;
				}
				
				if (isset($flux['args']['contexte']['voir']) && $flux['args']['contexte']['voir'] == 'noisettes' && autoriser('configurer', 'noizetier')) {
					$complements = recuperer_fond('noizetier-generer-bloc-voir-noisettes', $contexte, array('raw' => true));
				} else {
					$complements = recuperer_fond('noizetier-generer-bloc', $contexte, array('raw' => true));
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
				$bloc = substr($fond, 0, strpos($fond, '/'));
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
				$info_page = noizetier_lister_pages($page);
				if (isset($info_page['blocs'][$bloc])) {
					$complements = recuperer_fond('noizetier-generer-bloc-voir-noisettes', $contexte, array('raw' => true));
					$flux['data']['texte'] .= $complements['texte'];
				}
			}
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline boite_infos (SPIP)
 * 
 * Ajouter un lien pour configurer les noisettes de ce contenu précisément
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function noizetier_boite_infos($flux){
	include_spip('inc/autoriser');
	
	if (autoriser('configurernoisettes', $flux['args']['type'], $flux['args']['id'])) {
		include_spip('inc/presentation');
		
		// On cherche le nombre de noisettes déjà configurées pour ce contenu
		$nb = sql_countsel('spip_noisettes', array('objet = '.sql_quote($flux['args']['type']), 'id_objet = '.intval($flux['args']['id'])));
		if (!$nb) {
			$texte = _T('noizetier:noisettes_configurees_aucune');
		}
		elseif ($nb == 1) {
			$texte = _T('noizetier:noisettes_configurees_une');
		}
		else {
			$texte = _T('noizetier:noisettes_configurees_nb', array('nb'=>$nb));
		}
		
		$flux['data'] .= icone_horizontale($texte, parametre_url(parametre_url(generer_url_ecrire('noizetier_page'), 'id_objet', $flux['args']['id']), 'objet', $flux['args']['type']), 'noisette', $fonction="", $dummy="", $javascript="");
	}
	
	return $flux;
}

/**
 * Personnaliser le fond des formulaires.
 *
 * Ajout d'un lien vers la page de configuration
 * dans le formulaire de sélection de composition d'un objet
 */
function noizetier_formulaire_fond($flux) {
	// formulaire d'edition de la composition d'un objet
	if (isset($flux['args']['form']) and $flux['args']['form'] == 'editer_composition_objet') {
		$objet = isset($flux['args']['contexte']['objet']) ? $flux['args']['contexte']['objet'] : '';
		$composition = isset($flux['args']['contexte']['composition']) ? $flux['args']['contexte']['composition'] : '';
		$type_page = $objet.($composition ? '-'.$composition : '');
		$noizetier_compositions_meta = isset($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
		$noizetier_compositions_xml = array_keys(noizetier_lister_pages());

		// On vérifie que cette composition existe
		if ((isset($noizetier_compositions_meta[$objet][$composition]) and is_array($noizetier_compositions_meta[$objet][$composition]))
			or in_array($type_page, $noizetier_compositions_xml)
		) {
			$balise_img = charger_filtre('balise_img');
			$lien = generer_url_ecrire('noizetier_page', "page=$type_page");
			$alt = _T('noizetier:editer_configurer_page');
			$cherche = "/(<span[^>]*class=('|\")toggle_box_link[^>]*>)/is";
			$icone = inserer_attribut($balise_img(find_in_path('prive/themes/spip/images/noisette-16.png')), 'style', 'vertical-align:middle;');
			$remplace = '$1'."<a href=\"$lien\" title=\"$alt\">".$icone.'</a> ';
			$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
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
	$noizetier_compositions = isset($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
	if (!is_array($noizetier_compositions)) {
		$noizetier_compositions = array();
	}
	unset($noizetier_compositions['page']);
	$type = $flux['args']['type'];
	$informer = $flux['args']['informer'];

	include_spip('inc/texte');
	foreach ($noizetier_compositions as $t => $compos_type) {
		foreach ($compos_type as $c => $info_compo) {
			if ($informer) {
				$noizetier_compositions[$t][$c]['nom'] = typo($info_compo['nom']);
				$noizetier_compositions[$t][$c]['description'] = propre($info_compo['description']);
				if ($info_compo['icon'] != '') {
					$icone = $info_compo['icon'];
				} else {
					$info_page = noizetier_lister_pages($t);
					$icone = (isset($info_page['icon']) && $info_page['icon'] != '') ? $info_page['icon'] : 'composition-24.png';
				}
				$noizetier_compositions[$t][$c]['icon'] = noizetier_chemin_icone($icone);
			} else {
				$noizetier_compositions[$t][$c] = 1;
			}
		}
	}

	if ($type == '' and count($noizetier_compositions) > 0) {
		if (!is_array($flux['data'])) {
			$flux['data'] = array();
		}
		
		$flux['data'] = array_merge_recursive($flux['data'], $noizetier_compositions);
	} elseif (isset($noizetier_compositions[$type]) and count($noizetier_compositions[$type]) > 0) {
		if (!isset($flux['data'][$type]) or !is_array($flux['data'][$type])) {
			$flux['data'][$type] = array();
		}
		if (!is_array($noizetier_compositions[$type])) {
			$noizetier_compositions[$type] = array();
		}
		
		$flux['data'][$type] = array_merge_recursive($flux['data'][$type], $noizetier_compositions[$type]);
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

	return $plugins;
}

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
		$btn = recuperer_fond('prive/bouton/voir_noisettes');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);
	}

	return $flux;
}

// Lorsque l'on affiche la page admin_plugin, on supprime le cache des noisettes.
// C'est un peu grossier mais pas trouvé de pipeline pour agir à la mise à jour d'un plugin.
// Au moins, le cache est supprimé à chaque changement, mise à jour des plugins.

function noizetier_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];

	if ($exec == 'admin_plugin') {
		include_spip('inc/flock');
		include_spip('noizetier_fonctions');
		supprimer_fichier(_DIR_CACHE._CACHE_AJAX_NOISETTES);
		supprimer_fichier(_DIR_CACHE._CACHE_CONTEXTE_NOISETTES);
		supprimer_fichier(_DIR_CACHE._CACHE_INCLUSIONS_NOISETTES);
		supprimer_fichier(_DIR_CACHE._CACHE_DESCRIPTIONS_NOISETTES);
	}

	return $flux;
}
