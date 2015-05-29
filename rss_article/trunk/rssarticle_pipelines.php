<?php
/**
 * Pipelines utilisés par le plugin RSS en articles
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function rssarticle_taches_generales_cron($taches_generales){ 
	// si cfg dispo, on charge les valeurs
	if (!function_exists('lire_config'))
		include_spip('inc/config');
	$delai = intval(lire_config('rssarticle/cron_interval_value',600));
	if ($delai<10)
		$delai=10;		// securite pour les valeurs absurdes
	$taches_generales['rssarticle_copie'] = $delai;

	return $taches_generales;
}

/**
 * Insertion au centre des pages d'articles dans le privé
 * Affiche un formulaire d'édition de la licence de l'article
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte du pipeline modifié
 */
function rssarticle_affiche_milieu($flux) {
	if ($flux['args']['exec'] == 'site'){
		include_spip('inc/config');
		if (lire_config('rssarticle/mode') == "auto")
			$mode_auto=true;
		else
			$mode_auto=false;
		
		if (!$mode_auto) {
			$out .= "\n<div id='bloc_rssarticle'>";
			$out .= "\n". recuperer_fond('prive/contenu/rssarticle',array('id_syndic' => $flux["args"]["id_syndic"]),array('ajax'=>false));
			$out .= "\n</div>";
			if ($p=strpos($flux['data'],'<!--affiche_milieu-->'))
				$flux['data'] = substr_replace($flux['data'],$out,$p,0);
		}
	}
	return $flux;
}

?>