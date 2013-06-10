<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 * 
 * Fichier de définition des différents pipelines
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * On ajoute les js compilé et la css du player dans le public
 * 
 * @param $flux string
 * 		Le contenu du insert_head modifié
 * @return $flux string
 * 		Le contenu du insert_head modifié
 */function mediaspip_player_insert_head($flux){
	$flux .= '
<script src="'.produire_fond_statique('mediaspip_medias_init.js',array('lang'=>$GLOBALS['spip_lang'])).'" type="text/javascript"></script>
';
	return $flux;
}

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * On ajoute les js compilé et la css du player dans le privé
 * 
 * @param $flux string
 * 		Le contenu du insert_head modifié
 * @return $flux string
 * 		Le contenu du insert_head modifié
 */
function mediaspip_player_header_prive($flux){
	$flux .= '
<script src="'.produire_fond_statique('mediaspip_medias_init.js',array('lang'=>$GLOBALS['spip_lang'])).'" type="text/javascript"></script>
<link rel="stylesheet" href="'.direction_css(find_in_path('css/html5_controls.css')).'" type="text/css" media="all" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline jqueryui_plugins (jQuery UI)
 * On ajoute les sliders au chargement des js (et ses dépendances)
 * 
 * @param $flux array
 * 		L'array des plugins déjà inséré
 * @return $flux array
 * 		L'array des plugins mis à jour
 */
function mediaspip_player_jqueryui_plugins($plugins){
	$plugins[] = 'jquery.ui.slider';
	return $plugins;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute les différents plugins jquery dans le privé et public
 * 
 * @param $flux array
 * 		L'array des plugins déjà inséré
 * @return $flux array
 * 		L'array des plugins mis à jour
 */
function mediaspip_player_jquery_plugins($plugins){
	$plugins[] = _DIR_LIB_MOUSEWHEEL.'jquery.mousewheel.js';
	$plugins[] = 'javascript/flowplayer-3.2.12.min.js';
	$plugins[] = 'javascript/mediaspip_player.js';
	$plugins[] = 'javascript/mediaspip_fallback_flash.js';
	return $plugins;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * On ajoute la css de mediaspip_player dans l'espace public
 * 
 * @param $flux string
 * 		Le contexte du pipeline
 * @return $flux string
 * 		Le contexte du pipeline modifié
 */
function mediaspip_player_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(mediaspip_player_timestamp(find_in_path('css/html5_controls.css'))).'" type="text/css" media="all" />';
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * On vérifie les valeurs du formulaire de configuration
 * 
 * @param $flux array
 * 		Le contexte du pipeline
 * @return $flux array
 * 		Le contexte du pipeline modifié
 */
function mediaspip_player_formulaire_verifier($flux){
	if($flux['args']['form'] == 'configurer_mediaspip_player'){
		$numeriques = array('video_largeur_embed','video_hauteur_embed');
		foreach($numeriques as $numerique){
			if(_request($numerique) && !ctype_digit(_request($numerique)))
				$flux['data'][$numerique] = _T('mediaspip_player:erreur_valeur_int');
			if(!$flux['data'][$numerique] && _request($numerique) && (_request($numerique) > 2000))
				$flux['data'][$numerique] = _T('mediaspip_player:erreur_valeur_int_inf',array('nb'=>'2000'));
		}
		if(!$flux['data']['video_largeur_embed'] && _request('video_largeur_embed') && (_request('video_largeur_embed') < 200))
			$flux['data']['video_largeur_embed'] = _T('mediaspip_player:erreur_valeur_int_sup',array('nb'=>'200'));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * On purge le cache js pour que la nouvelle config soit prise en compte automatiquement
 * 
 * @param $flux array
 * 		Le contexte du pipeline
 * @return $flux array
 * 		Le contexte du pipeline modifié
 */
function mediaspip_player_formulaire_traiter($flux){
	if($flux['args']['form'] == 'configurer_mediaspip_player'){
		include_spip('inc/invalideur');
		$rep_js = _DIR_VAR.'cache-js/';
		purger_repertoire($rep_js);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * On affiche en dessous des documents les conversions
 *
 * @param array $flux
 * @return array $flux
 */
function mediaspip_player_recuperer_fond($flux){
	if ($flux['args']['fond']=='modeles/document_desc')
		$flux['data']['texte'] .= recuperer_fond('prive/inclure/document_desc_liste_conversions',$flux['args']['contexte']);
	return $flux;
}

/**
 * Insertion dans le pipeline medias_documents_visibles (Plugin medias)
 * On ajoute le fait que les documents ayant comme mode conversion soient visibles et non pas
 * supprimés des boucles documents
 *
 * @param array $flux
 * @return array $flux
 */
function mediaspip_player_medias_documents_visibles($flux){
	$flux[] = 'conversion';
	return $flux;
}

function mediaspip_player_timestamp($fichier){
	if ($m = filemtime($fichier))
		return "$fichier?$m";
	return $fichier;
}
?>