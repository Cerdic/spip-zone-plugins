<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

$table_des_traitements['BITRATE'][]= 'number_format(div(typo(%s),1000),0,""," ")';

?>