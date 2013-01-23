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
			'login'=>'matalo',
			'pass'=>'mot2passe', 
			'bio'=>'Par l\'Équipe [Mantalo conseil->http://www.mantalo.net/]',
			'nom_site'=>'Mantalo conseil',
			'url_site'=>'http://www.mantalo.net/',
			'nom'=>'mantalo',
			'email'=>'contact@perdu.com'
		*/)
	);
	return $liste_auteurs;
}
?>