<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

var_dump($GLOBALS['meta']);
$version_echoppe_locale = 0.1;
$version_echoppe_installee = $GLOBALS['meta']['echoppe_version'];

function echoppe_install($action){
	switch ($action){
		case 'test':
			//Contrôle du plugin à chaque chargement de la page d'administration
			// doit retourner true si le plugin est proprement installé et à jour, false sinon
			return ( && $version_echoppe_locale == $version_echoppe_installee);
		break;
		case 'install':
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//quand le plugin est activé et test retourne false
			include_spip('base/echoppe');
		break;
		case 'uninstall':
			//Appel de la fonction de suppression
			//quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
		break;
	}
}
?>