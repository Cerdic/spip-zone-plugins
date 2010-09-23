<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et son directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 *
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 * Fonctions spécifiques au squelette config_spipmotion.html
 **/

/**
 * Fonction de post-traitement du formulaire de configuration CFG
 * Vérifie principalement la présence du logiciel d'encodage
 */
function cfg_config_spipmotion_post_traiter(&$cfg){
	$modifs = $cfg->log_modif;
	include_spip('inc/metas');
	$valeurs = $cfg->val;

	$verifier_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
	$erreurs = $verifier_binaires($valeurs);

	if(!in_array('ffmpeg',$erreurs) && preg_match('/chemin/', $modifs, $matches)){
		/**
		 * On récupère les informations du nouveau ffmpeg
		 */
		$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
		$ffmpeg_infos(true);
	}

	if(count($erreurs) > 0){
		include_spip('inc/invalideur');
		suivre_invalideur('1');

		/**
		 * On force le rechargement de la page car on a récupéré de nouvelles infos sur ffmpeg
		 */
		$cfg->messages['redirect'] = self();
	}
}
?>