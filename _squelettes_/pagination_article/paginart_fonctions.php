<?php


function paginer_intertitres($texte){
	$t = explode('<h3 class="spip">',$texte);
	if (count($t)>2){
		$texte = array_shift($t);
		foreach($t as $p){
			$texte .= "<div class='section'><h3 class='spip'>".$p."</div>";
		}
		$texte = "<div class='paginer_intertitres'>".$texte."</div>";
	}
	return $texte;
}

function paginart_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('tabs.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('tabs.css').'" type="text/css" media="projection, screen" />
        <!-- Additional IE/Win specific style sheet (Conditional Comments) -->

        <!--[if lte IE 7]>
        <link rel="stylesheet" href="'.find_in_path('tabs-ie.css').'" type="text/css" media="projection, screen" />
        <![endif]-->
        ';
	return $flux;
}
?>
