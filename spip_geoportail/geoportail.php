<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Definition du path
*  
**/

$p2 = str_replace('\\','/',realpath(dirname(__FILE__))).'/';
define('_FULLDIR_PLUGIN_GEOPORTAIL', $p2);

include_spip('inc/geoportail_fonctions');

?>