<?php

//Conversion de date
function association_datefr($date) { 
	$split = split('-',$date); 
	$annee = $split[0]; 
	$mois = $split[1]; 
	$jour = $split[2]; 
return $jour.'-'.$mois.'-'.$annee; 
} 

//Validation d'adresse email
function association_validation_email($email) {
	$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   	// caractères autorisés avant l'arobase
	$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; 	// caractères autorisés après l'arobase (nom de domaine)
	$regex = '/^' . $atom . '+' .   				// Une ou plusieurs fois les caractères autorisés avant l'arobase
	'(\.' . $atom . '+)*' .        					// Suivis par zéro point ou plus
													// séparés par des caractères autorisés avant l'arobase
	'@' .                           				// Suivis d'un arobase
	'(' . $domain . '{1,63}\.)+' .  				// Suivis par 1 à 63 caractères autorisés pour le nom de domaine
													// séparés par des points
	$domain . '{2,63}$/i';          				// Suivi de 2 à 63 caractères autorisés pour le nom de domaine
// test de l'adresse e-mail
if (preg_match($regex, $email)) 
{return true;}
else {return false;}
}

//Creation d'un login
function association_cree_login($email) {
     $login = substr($email, 0, strpos($email, "@"));
     $login = strtolower($login);
     $login = ereg_replace("[^a-zA-Z0-9]", "", $login);     
		
     for ($i = 0; ; $i++) {
     	if ($i) $login = $login.$i;
     	else $login = $login;
     	$query = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login'");
     	if (!spip_num_rows($query)) break;
	     }		
	return $login;
  }
?>