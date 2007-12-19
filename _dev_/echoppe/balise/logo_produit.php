<?php

function balise_LOGO_PRODUIT($p){
	
	$logo = champ_sql('logo', $p);
	$p->code = "generer_logo($logo)";
	$p->interdire_script = false;
	return $p;	

}

?>
