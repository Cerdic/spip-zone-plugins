<?php

  function balise_CAMERAS__dist($p){
	$zone = explode(".",$GLOBALS['domaine_site']);
	preg_match(",^CAMERAS_(.*)?$,", $p->nom_champ, $regs);
	switch ($regs[1]){
		case "ZONE" :
			$p->code = "'$zone[0]'";
			break;
		case "DOMAINE" :
			if (sizeof($zone)<=2) {$p->code = '.'.$zone[1];}
			else {$p->code = '.'.$zone[1].'.'.$zone[2];}
			break;
		default: 
			$p->code = $GLOBALS['domaine_site'];
			break;
	}
	return $p;
}

?>