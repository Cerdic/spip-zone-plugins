<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_LOGO_CATEGORIE($p){
	
	$logo = champ_sql('logo', $p);
	$p->code = "generer_logo($logo)";
	$p->interdire_script = false;
	return $p;	

}

?>
