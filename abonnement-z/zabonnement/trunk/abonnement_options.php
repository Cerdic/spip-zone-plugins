<?php

//AJout de more dans les blocs 
$GLOBALS['z_blocs']=array('contenu','navigation','extra','head','more');
// activer le chargement parallele sur les blocs contenu et more
#define('_Z_AJAX_PARALLEL_LOAD','contenu,more');

# faut-il tracer abonnement dans tmp/abonnement.log et tmp/prive_abonnement.log
# a modifier dans mes_options.php
//define ('_DEBUG_ABONNEMENT', true);


?>
