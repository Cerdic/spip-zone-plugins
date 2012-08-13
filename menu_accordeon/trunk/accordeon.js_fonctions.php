<?php
function accordeon_reglages_standard_js($options){
	// insérer les réglages standards.
	$reglages = array(
		'icons'=>'false'
	);
	
	foreach ($reglages as $r=>$v){
		
		if (strpos($options,$r)===false){ // verifier que la valeur n'existe pas déjà dans les réglages de l'utilisateur
			$options .= ','.$r.':'.$v;
			
		}	
	}
	
	$options = ltrim($options,',');// supprimer le ',' inital
	return $options;
		
}
?>