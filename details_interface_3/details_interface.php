<?php


function details_interface_header_prive($flux){
	
	$claire = str_replace("#", "",$GLOBALS["couleur_claire"]);
	$foncee = str_replace("#", "",$GLOBALS["couleur_foncee"]);
	$lien = str_replace("#", "",$GLOBALS["couleur_lien"]);
	
	
	$contexte = array(
		"claire" => $claire, 
		"foncee" => $foncee,
		"lien" => $lien
	);


	$flux .= "<link rel='stylesheet' type='text/css' href='../index.php?page=details.css&claire=$claire&foncee=$foncee&lien=$lien&var_mode=recalcul'  />";
	return $flux;
}

?>