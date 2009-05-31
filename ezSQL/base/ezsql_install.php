<?php
/*
    This file is part of ezSQL.

    ezSQL is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    ezSQL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SIOU; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
    Copyright 2007, 2008 - Ghislain VLAVONOU, Yannick EDAHE, Cedric PROTIERE
*/

  /*****                                *****
   ***** Installation de la table ezSQL *****
   *****                                *****/
   //include('../exec/inc-traitements.php');
 
function ezsql_install($action){
	return true;
    switch ($action){
        case 'test':
            //Contrôle du plugin à chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement installé et à jour, false sinon
        	$result=spip_query("SHOW TABLE LIKE 'ezsql'");
        	$nb=spip_num_rows($result);
        	if($nb==0) return false;
        	else return true;
        break;
        case 'install':
            //Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
            //quand le plugin est activé et test retourne false
            $sql="CREATE TABLE IF NOT EXISTS `ezsql` (\n"
				."`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,\n"
				."`requete` TEXT NOT NULL ,\n"
				."`login` VARCHAR( 64 ) NOT NULL ,\n"
				."`public` BOOL NOT NULL\n"
				.") ENGINE = MYISAM COMMENT = 'Requetes ezSQL';"
			spip_query($sql);
        break;
        case 'uninstall':
            //Appel de la fonction de suppression
            //quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
            spip_query("DROP TABLE ezsql");
        break;
    }
    return true;
}
?>
