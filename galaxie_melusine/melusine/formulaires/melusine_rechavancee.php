<?php 
include_spip('inc/config');
function formulaires_melusine_rechavancee_charger(){
	$valeurs = array();
	return $valeurs;
}

function formulaires_melusine_rechavancee_verifier(){
	$erreurs = array();
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}





function formulaires_melusine_rechavancee_traiter(){
	effacer_config("melusine_rechavancee");		
	$groupes=_request('groupes',$tableau);
	$couleur_fond=_request('couleur_fond');
	$couleur_bord=_request('couleur_bord');
	ecrire_config('melusine_rechavancee/couleur_fond',$couleur_fond);
	ecrire_config('melusine_rechavancee/couleur_bord',$couleur_bord);
	$i=0;
		foreach ($groupes as $value){			
			$chemin="melusine_rechavancee/$value";
			
			
				ecrire_config($chemin,$value);
				
				$i++;
			}
		
		
	return false;	
	
}



?>