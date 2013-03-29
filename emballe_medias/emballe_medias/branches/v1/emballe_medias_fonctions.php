<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Fonctions utilisables dans les squelettes
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 * Affiche sur le formulaire d'upload une liste d'extension de la sorte :
 * *.ext, *.ext...
 *
 * @param array $array L'array des extensions de la configuration
 */
function emballe_medias_liste_extensions($array,$sep='; *.',$debut='*.'){
	if(!is_array($array)){
		return _T('emballe_medias:configurer_les_extensions');
	}
	else{
		$liste = implode($sep,$array);
		if(!$liste){
			return _T('emballe_medias:configurer_les_extensions');
		}
		else{
			$liste = $debut.$liste;
		}
	}
	return $liste;
}

function emballe_medias_liste_mimes($array){
	if(!is_array($array)){
		return false;
	}
	$mimes_finaux = array();
	$mimes = sql_select('*','spip_types_documents',sql_in('extension',$array));
	while($mime=sql_fetch($mimes)){
		$mimes_finaux[] = $mime['mime_type'];
	}
	$mimes_finaux = array_unique($mimes_finaux);
	$ret = implode(', ',$mimes_finaux);
	return $ret;
}

/**
 * Encode le fichier de langue javascript du formulaire d'upload proprement
 *
 * @return string Le json correctement encodé
 * @param string $fond Le squelette de base du json
 * @param string $lang Le code de langue  désiré
 */
function emballe_medias_json_encode($fond,$lang){
	$json = recuperer_fond($fond,array('lang'=>$lang?$lang:'fr'));

	include_spip('inc/charsets');
	$json = unicode2charset(html2unicode($json));
	return $json;
}

/**
 * La génération des extensions, utilisée par la balise #FORM_TYPE
 *
 * @return array L'array des extensions autorisées pour un type qui existe,
 * sinon un array avec toutes les extensions possibles
 * @param string $type[optional] Le type de formulaire désiré (image,video,audio,texte par défaut)
 */
function emballe_medias_generer_extensions($type=NULL){
	if($type !== NULL && (lire_config('emballe_medias/fichiers/gerer_types') == 'on')){
		if(($ext = lire_config('emballe_medias/fichiers/fichiers_'.$type.'s')) && ($ext !== NULL)){
			$extensions = $ext;
		}
		else if(defined('_FORM_TYPE_'.strtoupper($type)) && ($ext = constant('_FORM_TYPE_'.strtoupper($type)))){
			$extensions = explode(',',$ext);
		}
		else if(!$extensions){
			$extensions = emballe_medias_generer_extensions();
		}
	}else if(
			(lire_config('emballe_medias/fichiers/gerer_types') != 'on')
			OR (lire_config('emballe_medias/types/autoriser_normal') == 'on')
		){
		$extensions = array();
		if(is_array(lire_config('emballe_medias/fichiers/fichiers_images'))){
			$extensions = array_merge($extensions,lire_config('emballe_medias/fichiers/fichiers_images'));
		}
		if(is_array(lire_config('emballe_medias/fichiers/fichiers_videos'))){
			$extensions = array_merge($extensions,lire_config('emballe_medias/fichiers/fichiers_videos'));
		}
		if(is_array(lire_config('emballe_medias/fichiers/fichiers_audios'))){
			$extensions = array_merge($extensions,lire_config('emballe_medias/fichiers/fichiers_audios'));
		}
		if(is_array(lire_config('emballe_medias/fichiers/fichiers_textes'))){
			$extensions = array_merge($extensions,lire_config('emballe_medias/fichiers/fichiers_textes'));
		}
		if(!$extensions){
			$extensions = explode(',',_FORM_TYPE_DEFAULT);
		}
	}
	return $extensions;
}

/**
 * Calcule la clé d'action pour l'action de téléchargement
 * @param string $texte
 */
function em_calculer_cle_action($texte){
	include_spip('inc/securiser_action');
	return calculer_cle_action($texte);
}
?>