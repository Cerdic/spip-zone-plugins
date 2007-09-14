<?php
/*
   Auteur chryjs (c) 2007
   Based on Beurt work : http://www.spip-contrib.net/Faire-migrer-un-site-statique-vers
   Plugin for spip 1.9.2
   Licence GNU/GPL
*/

function migre_static_install($action)
{
switch ($action) {
 case 'test' : /* test pour savoir si les actions sont nécessaires */
	break;
 case 'install' :
	break;
 case 'uninstall' :
	break;
 }
} /* migre_static_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function migre_static_uninstall(){
}

?>
