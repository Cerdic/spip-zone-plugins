<?php

//AJout de more dans les blocs 
$GLOBALS['z_blocs']=array('contenu','navigation','extra','head','more');
// activer le chargement parallele sur les blocs contenu et more
#define('_Z_AJAX_PARALLEL_LOAD','contenu,more');

?>
