<?php
function pivotsyndic_pre_syndication($flux) {
	if ($dir = opendir(_DIR_PLUGIN_PIVOTSYNDIC.'/inc/filtres/')) {
	  while (false !== ($fichier = readdir($dir))) {
	    if (is_file(_DIR_PLUGIN_PIVOTSYNDIC.'/inc/filtres/'.$fichier)) {
        $filtre = basename($fichier, '.php');
	    	include_spip('inc/filtres/'.$filtre);
		    $flux = $filtre($flux);
      }
    }
    closedir($dir);
  }

  return $flux;
}
?>