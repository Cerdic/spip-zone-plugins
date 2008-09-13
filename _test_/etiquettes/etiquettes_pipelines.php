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
	if(isset($GLOBALS['meta']['plugin']['SelecteurGenerique'])){
		$iutil = find_in_path('javascript/iutil.js');
		$iautocompleter = find_in_path('javascript/iautocompleter.js');
		$selecteur_generique = '<script type="text/javascript" src="$iutil"></script>';
		$selecteur_generique .= '<script type="text/javascript" src="$iautocompleter"></script>';
	}
	
	$flux .= <<<EOS
	<link rel="stylesheet" type="text/css" media="all" href="$css" />
	$selecteur_generique
	<script type="text/javascript" src="$etiquettes"></script>
		
EOS;
	
	return $flux;
	
}

function etiquettes_affichage_final($page){
	
	if (!stripos($page, '_etiquettes'))
		return $page;
	
	$iutil = find_in_path('javascript/iutil.js');
	$iautocompleter = find_in_path('javascript/iautocompleter.js');
	$etiquettes = find_in_path('javascript/etiquettes.js');
	$css = find_in_path('css/etiquettes.css');
	
	$incHead = <<<EOS
	
	<link rel="stylesheet" type="text/css" media="all" href="$css" />
		
EOS;
	
	if ($aide_ajax = strpos($page, 'appliquer_selecteur_cherche_mot')){
		$incHead .= <<<EOS
		
	<script type='text/javascript' src='$iutil'></script>
	<script type='text/javascript' src='$iautocompleter'></script>
			
EOS;
	}
	if($aide_ajax OR strpos($page, 'id="popular_tags')){
		$incHead .= <<<EOS
		
	<script type='text/javascript' src='$etiquettes'></script>
			
EOS;
	}
	
	
	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
	
}

?>
