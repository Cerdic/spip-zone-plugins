<?php

function paginart3_BarreTypoEnrichie_boutonsavances($paramArray){
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	return "&nbsp;".bouton_barre_racc ("barre_raccourci('\n\n{p{','}p}\n\n',".$paramArray[0].")", _DIR_PLUGINS.end($p).'/img_pack/icones_barre/intertitre_p.png', _T('paginart3:barre_intertitre_p'), $paramArray[1])."&nbsp;";	
}

function paginart3_nettoyer_raccourcis_typo($texte){
	$texte = preg_replace(',{p{,','',$texte);
	$texte = preg_replace(',}p},','',$texte);
	return $texte;
}

function paginart3_paginer($texte){
	global $debut_intertitre_p,$fin_intertitre_p;
	$t = explode("<h3 class=\"paginart3\">",$texte);
	if (count($t)>2){
		$texte = array_shift($t);
		foreach($t as $p){
			$p = str_replace("</h3>","</span></h3>",$p);
			$texte .= "<div class='section'><h3 class=\"paginart3\"><span class='titre_onglet'>".$p."</div>";
		}
		$texte = "<div class='paginer_intertitres'>".$texte."</div>";
	}
	return $texte;
}

function paginart3_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('paginart3_tabs.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('paginart3_tabs.css').'" type="text/css" media="projection, screen" />
        <!-- Additional IE/Win specific style sheet (Conditional Comments) -->

        <!--[if lte IE 7]>
        <link rel="stylesheet" href="'.find_in_path('paginart3_tabs-ie.css').'" type="text/css" media="projection, screen" />
        <![endif]-->
        ';
	return $flux;
}
?>