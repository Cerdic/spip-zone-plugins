<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Fichiers d'options spécifique au plugin (chargé à chaque hit)
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_FORM_TYPE_IMAGE','jpg,jpeg,gif,png');
if(!function_exists('lire_config'))
	include_spip('inc/config');

define('_FORM_TYPE_VIDEO',implode(',', lire_config('emballe_medias/fichiers/fichiers_videos',array('mp4,flv,mov,avi'))));
define('_FORM_TYPE_AUDIO',implode(',', lire_config('emballe_medias/fichiers/fichiers_audios',array('wav,mp3'))));
define('_FORM_TYPE_TEXTE','pdf,doc,docx,odt,xls,xlsx,ods');
define('_FORM_TYPE_DEFAULT','jpg,jpeg,gif,png,mp4,flv,mov,avi,wav,mp3,pdf,doc,docx,odt');

/**
 * On active l'auto rotation selon EXIF
 * cf : inc/ajouter_documents ligne 313
 */
define('_TOURNER_SELON_EXIF', true);

?>
