<?php
/*
* Configuration de SPIP pour auteur_automatique
* Attention, fichier en UTF-8 sans BOM
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function liste_auteurs_automatiques() {
	$liste_auteurs = array(
		array( /* Copiez ce fichier dans squelettes/inc/ et modifiez-le en conséquence
			'statut'=> '0minirezo',
			'webmestre'=>'oui',
			'login'=>'login',
			'pass'=>'mot2passe', 
			'bio'=>'',
			'nom_site'=>'',
			'url_site'=>'',
			'nom'=>'votre nom',
			'email'=>'contact@perdu.com'
		*/)
	);
	return $liste_auteurs;
}
?>