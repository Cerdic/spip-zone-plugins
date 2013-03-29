<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Fichiers d'options spécifique au plugin (chargé à chaque hit)
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_FORM_TYPE_IMAGE','jpg,gif,png');
if(function_exists('lire_config')){
	define('_FORM_TYPE_VIDEO',implode(',', lire_config('emballe_medias/fichiers/fichiers_videos',array('mp4,flv,mov,avi'))));
	define('_FORM_TYPE_AUDIO',implode(',', lire_config('emballe_medias/fichiers/fichiers_audios',array('wav,mp3'))));
}
define('_FORM_TYPE_TEXTE','pdf,doc,odt,xls,ods');
define('_FORM_TYPE_DEFAULT','jpg,gif,png,mp4,flv,mov,avi,wav,mp3,pdf,doc,odt');

/**
 * On active l'auto rotation selon EXIF
 * cf : inc/ajouter_documents ligne 313
 */
define('_TOURNER_SELON_EXIF', true);

/**
 * Déclaration des pipelines du plugin
 */

// génération de valeurs
$GLOBALS['spip_pipeline']['em_types']="";
// actions
$GLOBALS['spip_pipeline']['em_post_upload_medias']="";

?>