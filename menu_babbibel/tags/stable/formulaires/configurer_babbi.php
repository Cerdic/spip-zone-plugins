<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_configurer_babbi_verifier_dist() {

	$erreurs = array();
  $var = _request('nb_articles');
  
    $numerique = is_numeric($var);  
    $positif = $var == abs($var);
    $entier = $var == intval($var);
  	

				//nb_articles doit être un numérique				
					if(!$numerique && $var!=null) { $numerique = 0;
								$erreurs['nb_articles'] = _T('babbi:que_des_nombres_ici');}
				//nb_articles doit être un nombre positif								
					if(!$positif){ $positif = 0;
								$erreurs['nb_articles'] = _T('babbi:que_des_nombres_ici');}
				//nb_articles doit être un nombre entier
					if(!$entier) { $entier = 0;
								$erreurs['nb_articles'] = _T('babbi:que_des_nombres_ici');}


											
					if (count($erreurs))
                $erreurs['message_erreur'] = _T('babbi:message_erreur');
                
	return $erreurs;
}
?>