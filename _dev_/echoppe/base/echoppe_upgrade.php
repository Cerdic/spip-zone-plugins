<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_install($action){
	switch ($action){
		case 'test':
			//Contr�le du plugin � chaque chargement de la page d'administration
			// doit retourner true si le plugin est proprement install� et � jour, false sinon
		break;
		case 'install':
			//Appel de la fonction d'installation. Lors du clic sur l'ic�ne depuis le panel.
			//quand le plugin est activ� et test retourne false
		break;
		case 'uninstall':
			//Appel de la fonction de suppression
			//quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
		break;
	}
}
?>