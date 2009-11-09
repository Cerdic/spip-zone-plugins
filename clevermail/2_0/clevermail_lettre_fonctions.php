<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function extraire($texte,$quoi){
	if ($quoi=='body'){
		if (preg_match(",<{$quoi}>(.*)</{$quoi}>,s", $texte, $t)) {
				    $retour = trim($t[1]);
				  }
	}else{
			if (preg_match(",<{$quoi}>(.*)</{$quoi}>,", $texte, $t)) {
				    $retour = trim($t[1]);
				  }
	}
	return $retour;
}

?>