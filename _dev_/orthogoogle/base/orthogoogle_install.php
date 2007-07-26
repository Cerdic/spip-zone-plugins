<?php
	function orthogoogle_install($action){
		switch ($action){
		case 'test':
			//rien à faire donc tout va bien 
			return true;
			break;
		case 'install':
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//est ce qu'une config existe ?
			if(!lire_config('orthogoogle')){
				//par défaut juste le champ d'id text_area est corrigeable
				ecrire_meta('orthogoogle','a:1:{s:9:"text_area";s:2:"on";}');		
			}
			//quoiqu'il arrive c'est ok
			return true;			
			break;
		case 'uninstall':
			//Appel de la fonction de suppression
			effacer_meta('orthogoogle');
			//tout s'est deroulé comme il faut
			return true;
			break;
		}
	}
?>
