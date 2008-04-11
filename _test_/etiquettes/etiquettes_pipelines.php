<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes  #
#-----------------------------------------------------------------#

function etiquettes_affichage_final($page){
	
	if (!strpos($page, 'formulaire_etiquettes'))
		return $page;
	
	$iutil = find_in_path('javascript/iutil.js');
	$iautocompleter = find_in_path('javascript/iautocompleter.js');
	$css = find_in_path('css/etiquettes.css');
	
	$incHead = <<<EOS
		<link rel="stylesheet" type="text/css" media="all" href="$css" />
EOS;
	
	if (strpos($page, 'appliquer_selecteur_cherche_mot')){
		$incHead .= <<<EOS
			<script type='text/javascript' src='$iutil'></script>
			<script type='text/javascript' src='$iautocompleter'></script>
EOS;
	}
	
	
	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
	
}

?>
