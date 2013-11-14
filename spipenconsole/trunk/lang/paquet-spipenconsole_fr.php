<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'spipenconsole_description' => 'Pour utilisateur linux, gestion des plugins en ligne de commande. ce plugin s\'installe dans le repertoire plugins, il possede un fichier paquet.xml pour ne pas que spip crie au scandale mais il ne sert à rien.
Utilisation : rendre executable le fichier spipenconsole : chmod u+x spipenconsole
puis : ./spipenconsole avec des options :
-h : aide
-l : liste tous les plugins et marque les plugins actifs
-a : active une liste de plugins
-d : desactive une liste de plugins
-t : realise un svn checkout du plugin sous la forme : nom,repertoire
-f : realise un svn checkout de l\'ensemble des plugins du fichier telecharger.csv
l\'affichage fonctionne pour l\'instant uniquement si le fichier paquet.xml se trouve dans le repertoire de premier niveau',
);

?>
