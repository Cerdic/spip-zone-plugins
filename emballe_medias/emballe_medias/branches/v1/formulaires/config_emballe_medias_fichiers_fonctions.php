<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Fonctions spécifiques au squelette config_emballe_medias_fichiers.html
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification du formulaire de configuration
 * Vérifie la taille d'upload
 * @param object $cfg
 */
function cfg_config_emballe_medias_fichiers_verifier(&$cfg){
	$max_size = str_replace('M','',@ini_get('upload_max_filesize'));
	$max_post = str_replace('M','',@ini_get('post_max_size'));
	
	if(is_numeric($max_size) && ($max_size > 0) && is_numeric($max_post) && ($max_post > 0)){
		if($cfg->val['file_size_limit'] && (($cfg->val['file_size_limit'] > $max_size) OR ($cfg->val['file_size_limit'] > $max_post))){
			$erreur['file_size_limit'] = _T('emballe_medias:erreur_filesize_limit',array('taille_max' => $max_size));
		}
	}
	if(count($erreur) > 0){
		$erreur['message_erreur'] = _T('emballe_medias:verifier_formulaire');
	}
	return $erreur;
}

/**
 * Fonction de post-traitement du formulaire de configuration CFG
 */
function cfg_config_emballe_medias_fichiers_post_traiter(&$cfg){
	$modifs = $cfg->log_modif;
	if(preg_match('/gerer_types/', $modifs, $matches)){
		/**
		 * On invalide le cache pour le cas de la modification de la
		 * configuration de gestion des types
		 * Mets à jour les menus d'emballe médias principalement
		 */
		include_spip('inc/invalideur');
		suivre_invalideur('1');

		/**
		 * Si les types n'ont jamais été configurés, on les active tous
		 */
		$valeurs = $cfg->val;
		if(($valeurs['gerer_types'] == 'on') && !is_array(lire_config('emballe_medias/types/types_dispos'))){
			$config = lire_config('emballe_medias/types',array());
			$config['types_dispos'] = array('audio','image','texte','video');
			include_spip('inc/metas');
			ecrire_config('emballe_medias/types', $config);
		}
		/**
		 * On redirige le formulaire pour rafraichir la page
		 */
		$cfg->messages['redirect'] = self();
	}
}
?>
