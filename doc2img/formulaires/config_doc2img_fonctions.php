<?php 
/**
 * Fonctions associées au formulaire de configuration de doc2img
 */

/**
 * Fonction de vérification du formulaire de configuration CFG
 */
function cfg_config_doc2img_verifier(&$cfg){
	$modifs = $cfg->log_modif;
	if (class_exists('Imagick')) {
		if(!is_array($formats = lire_config('doc2img_imagick_extensions'))){
			include_spip('inc/metas');
			$imagick = new Imagick();
			$formats = $imagick->queryFormats();
			ecrire_metas('doc2img_imagick_extensions',serialize($formats));
		}
		$valeurs = $cfg->val;
		if($valeurs['format_document']){
			$formats_choisis = explode(',',$valeurs['format_document']);
			$diff = array_diff(array_map('strtolower',$formats_choisis),array_map('strtolower',$formats));
		}
		if(count($diff) > 1){
			$cfg->messages['erreurs']['format_document'] = _T('doc2img:erreur_formats_documents',array('types'=>implode(',',$diff)));
		}else if(count($diff) == 1){
			$cfg->messages['erreurs']['format_document'] = _T('doc2img:erreur_format_document',array('type'=>implode(',',$diff)));
		}
	}
}
?>