<?php

$GLOBALS['marqueur_skel'] = (isset($GLOBALS['marqueur_skel']) ?  $GLOBALS['marqueur_skel'] : '').":bootstrap3";
if (strncmp($GLOBALS['spip_version_branche'],"3.0",3)==0){
	_chemin(_DIR_PLUGIN_BOOTSTRAP3."spip3/");
}