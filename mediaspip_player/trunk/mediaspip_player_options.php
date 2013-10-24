<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2013 - Distribué sous licence GNU/GPL
 * 
 * Fichier d'options
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * On définit _DIR_LIB_MOUSEWHEEL
 * https://github.com/brandonaaron/jquery-mousewheel/tags
 */
define('_DIR_LIB_MOUSEWHEEL','lib/jquery-mousewheel-3.1.4/');

/**
 * On force le fait que l'on puisse ajouter des documents sur les documents
 */
$GLOBALS['meta']['documents_objets'] = $GLOBALS['meta']['documents_objets'].',spip_documents';

?>