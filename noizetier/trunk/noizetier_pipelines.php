<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_header_prive($flux){
	$css = direction_css(find_in_path('css/noizetier.css'));
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}


/**
 * Pipeline recuperer_fond pour ajouter les noisettes
 *
 * @param array $flux
 * @return array
 */
function noizetier_recuperer_fond($flux){
	if (defined('_NOIZETIER_RECUPERER_FOND')?_NOIZETIER_RECUPERER_FOND:true) {
		include_spip('noizetier_fonctions');
		$fond = $flux['args']['fond'];
		$composition = $flux['args']['contexte']['composition'];
		// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
		// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
		if ($composition!='' AND noizetier_page_composition($fond)=='' AND noizetier_page_type($fond)!='page')
			$fond .= '-'.$composition;
		
		// Tester l'installation du noizetier pour éviter un message d'erreur à l'installation
		if (isset($GLOBALS['meta']['noizetier_base_version'])) {
			if ($flux['args']['contexte']['voir']=='noisettes' && !function_exists('autoriser'))
				include_spip('inc/autoriser');	 // si on utilise le formulaire dans le public
			if (in_array($fond,noizetier_lister_blocs_avec_noisettes())) {
				$contexte = $flux['data']['contexte'];
				$contexte['bloc'] = substr($fond,0,strpos($fond,'/'));
				if ($flux['args']['contexte']['voir']=='noisettes' && autoriser('configurer','noizetier'))
					$complements = recuperer_fond('noizetier-generer-bloc-voir-noisettes',$contexte,array('raw'=>true));
				else
					$complements = recuperer_fond('noizetier-generer-bloc',$contexte,array('raw'=>true));
				$flux['data']['texte'] .= $complements['texte'];
			}
			elseif ($flux['args']['contexte']['voir']=='noisettes' && autoriser('configurer','noizetier')) { // Il faut ajouter les blocs vides en mode voir=noisettes
				$contexte = $flux['data']['contexte'];
				$bloc = substr($fond,0,strpos($fond,'/'));
				$contexte['bloc'] = $bloc;
				$page = isset($contexte['type']) ? $contexte['type'] : '';
				$page .= (isset($contexte['composition']) && $contexte['composition']) ? '-'.$contexte['composition'] : '';
				$info_page = noizetier_lister_pages($page);
				if (isset($info_page['blocs'][$bloc])) {
					$complements = recuperer_fond('noizetier-generer-bloc-voir-noisettes',$contexte,array('raw'=>true));
					$flux['data']['texte'] .= $complements['texte'];
				}
			}
		}
	}
	return $flux;
}

/**
 * Pipeline compositions_lister_disponibles pour ajouter les compositions du noizetier
 *
 * @param array $flux
 * @return array
 */

function noizetier_compositions_lister_disponibles($flux){
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (!is_array($noizetier_compositions))
		$noizetier_compositions = array();
	unset($noizetier_compositions['page']);
	$type = $flux['args']['type'];
	$informer = $flux['args']['informer'];
	
	include_spip('inc/texte');
	foreach($noizetier_compositions as $t => $compos_type)
		foreach($compos_type as $c => $info_compo) {
			if($informer) {
				$noizetier_compositions[$t][$c]['nom'] = typo($info_compo['nom']);
				$noizetier_compositions[$t][$c]['description'] = propre($info_compo['description']);
				if ($info_compo['icon']!='')
					$icone = $info_compo['icon'];
				else {
					$info_page = noizetier_lister_pages($t);
					$icone = (isset($info_page['icon']) && $info_page['icon']!='') ? $info_page['icon'] : 'composition-24.png';
				}
				$noizetier_compositions[$t][$c]['icon'] = noizetier_chemin_icone($icone);
			}
			else
				$noizetier_compositions[$t][$c] = 1;
			}
	
	if ($type=='' AND count($noizetier_compositions)>0) {
		if (!is_array($flux['data']))
			$flux['data'] = array();
		$flux['data'] = array_merge_recursive ($flux['data'],$noizetier_compositions);
	}
	elseif (count($noizetier_compositions[$type])>0) {
		if (!is_array($flux['data'][$type]))
			$flux['data'][$type] = array();
		if (!is_array($noizetier_compositions[$type]))
			$noizetier_compositions[$type] = array();
		$flux['data'][$type] = array_merge_recursive ($flux['data'][$type],$noizetier_compositions[$type]);
	}
	return $flux;
}

/**
 * Pipeline styliser pour les compositions du noizetier de type page si celles-ci sont activées
 *
 * @param array $flux
 * @return array
 */
function noizetier_styliser($flux){
	if(defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') AND _NOIZETIER_COMPOSITIONS_TYPE_PAGE) {
		$squelette = $flux['data'];
		$fond = $flux['args']['fond'];
		$ext = $flux['args']['ext'];
		// Si on n'a pas trouvé de squelette
		if (!$squelette) {
			$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			// On vérifie qu'on n'a pas demandé une composition du noizetier de type page et qu'on appele ?page=type
			if (isset($noizetier_compositions['page'][$fond])) {
				$flux['data'] = substr(find_in_path("page.$ext"), 0, - strlen(".$ext"));
				$flux['args']['composition'] = $fond;
			}
		}
	}
	return $flux;
}

/**
 * Pipeline jqueryui_forcer pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function noizetier_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.mouse";
	$plugins[] = "jquery.ui.sortable";
	$plugins[] = "jquery.ui.droppable";
	$plugins[] = "jquery.ui.draggable";
	return $plugins;
}


function noizetier_noizetier_lister_pages($flux){return $flux;}
function noizetier_noizetier_blocs_defaut($flux){return $flux;}
function noizetier_noizetier_config_export($flux){return $flux;}
function noizetier_noizetier_config_import($flux){return $flux;}

// les boutons d'administration : ajouter le mode voir=noisettes
function noizetier_formulaire_admin($flux) {
	if (autoriser('configurer','noizetier')) {
		$btn = recuperer_fond('prive/bouton/voir_noisettes');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);
	}
	return $flux;
}

// Lorsque l'on affiche la page admin_plugin, on supprime le cache des noisettes.
// C'est un peu grossier mais pas trouvé de pipeline pour agir à la mise à jour d'un plugin.
// Au moins, le cache est supprimé à chaque changement, mise à jour des plugins.

function noizetier_affiche_milieu($flux) {
	$exec = $flux["args"]["exec"];
	
	if ($exec == "admin_plugin") {
		include_spip('inc/flock');
		include_spip('noizetier_fonctions');
		supprimer_fichier(_DIR_CACHE . _CACHE_AJAX_NOISETTES);
		supprimer_fichier(_DIR_CACHE . _CACHE_CONTEXTE_NOISETTES);
		supprimer_fichier(_DIR_CACHE . _CACHE_INCLUSIONS_NOISETTES);
		supprimer_fichier(_DIR_CACHE . _CACHE_DESCRIPTIONS_NOISETTES);
	}

	return $flux;
}

?>
