<?php
$gmaps_is_uninstalling=false;

function gmaps_install($action){
    switch ($action){
        case 'test':
            //Contr�le du plugin � chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement install� et � jour, false sinon
            global $gmaps_is_uninstalling;
            if(!$gmaps_is_uninstalling)
            {	spip_query('CREATE TABLE IF NOT EXISTS `spip_gmappoints` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`lat` DOUBLE NOT NULL ,
					`lng` DOUBLE NOT NULL ,
					`html` TEXT NOT NULL
					) ENGINE = MYISAM ;');            
            }
			return !$gmaps_is_uninstalling;            
        break;
        case 'install':
            //Appel de la fonction d'installation. Lors du clic sur l'ic�ne depuis le panel.
            //quand le plugin est activ� et test retourne false
            die('AAAAaaaaaahhhhh c\'est comme �a que �a fonctionne !!!');
        break;
        case 'uninstall':
            //Appel de la fonction de suppression
            //quand l'utilisateur clique sur "supprimer tout" (disponible si test retourne true)
            global $gmaps_is_uninstalling;
            $gmaps_is_uninstalling=true;
            spip_query('DROP TABLE `spip_gmappoints`');
        break;
    }
}

?>