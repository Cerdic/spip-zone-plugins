<?php
/**
 * genie / cron
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function rssarticle_taches_generales_cron($taches_generales){
	$delai =  60*10; // valeur defaut: ts les 10 min 
	// si cfg dispo, on charge les valeurs
	if (function_exists(lire_config))  {
		if (lire_config('rssarticle/cron_interval_value')!="") {	// verifier si champs CFG a ete renseigne sur ce plugin (retro-compat)
			$delai = intval(lire_config('rssarticle/cron_interval_value')); 
			if ($delai<10)
				$delai=10;		// securite pour les valeurs absurdes
		}
	}
	$taches_generales['rssarticle_copie'] = $delai;

	return $taches_generales;
}

/**
 * Insertion au centre des pages d'articles dans le privé
 * Affiche un formulaire d'édition de la licence de l'article
 *
 * @param array $flux Le contexte du pipeline
 */
function rssarticle_affiche_milieu($flux) {
	if ($flux['args']['exec'] == 'site'){
		include_spip('inc/config');
		if (lire_config('rssarticle/mode')=="auto") $mode_auto=true; else  $mode_auto=false;
		
		if (!$mode_auto) {
			$contexte['id_syndic'] = $flux["args"]["id_syndic"];
			//$out = debut_cadre_relief(_DIR_PLUGIN_RSSARTICLE."prive/themes/spip/images/rssarticle-32.png", true, '',_T("rssarticle:activer_recopie_intro"));
			$out .= "\n<div id='bloc_rssarticle'>";
			$out .= "\n". recuperer_fond('prive/contenu/rssarticle',$contexte,array('ajax'=>false));
			$out .= "\n</div>";
			//$out .= "\n". fin_cadre_relief(true);
			if ($p=strpos($flux['data'],'<!--affiche_milieu-->'))
				$flux['data'] = substr_replace($flux['data'],$out,$p,0);
		}
	}
	return $flux;
}

?>