<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes  #
#-----------------------------------------------------------------#

function etiquettes_insert_head($flux){

	$etiquettes = find_in_path('javascript/etiquettes.js');
	$css = find_in_path('css/etiquettes.css');
	$f = chercher_filtre('info_plugin');
	if ($f('SelecteurGenerique', 'est_actif')) {
		$autocomplete = find_in_path('javascript/jquery.autocomplete.js');
		$selecteur_generique = '<script type="text/javascript" src="'.$autocomplete.'"></script>';
	}
	
	$flux .= <<<EOS
	<link rel="stylesheet" type="text/css" media="all" href="$css" />
	$selecteur_generique
	<script type="text/javascript" src="$etiquettes"></script>
		
EOS;
	
	return $flux;
	
}

?>
