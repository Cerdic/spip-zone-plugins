#!/bin/sh
### FICHIER DE DESCRIPTION DE LA MIGRATION VERS PAQUET.XML
### ------------------------------------------------------
### - Date : 02-Jan-2012 14:51:07
### - Fichier d'origine : ../plugins/recherche_sti/plugin.xml
### - Contient les informations et les commandes SVN pour
###   rendre effective la migration utilisant les fichiers produits.
###   Pour se garder de toute erreur de manipulation
###   les commandes sont en commentaire.
### ------------------------------------------------------

### Ajout du fichier paquet.xml au depot.
### On conserve le fichier plugin.xml tout le temps de la migration !
# svn add paquet.xml

### Ajout au depot des fichiers de langue donnant slogan et description du plugin. 
### Attention la liste des langues provient des traductions (avec multi)
### de la description dans plugin.xml et non des modules de langue du plugin !
# svn add lang/paquet-recherche_sti_fr.php

### La disparition des balises options, fonctions et install au profit 
### d'un nommage standard d'un fichier unique exige parfois de renommer 
### les fichiers, voire de les fusionner.
### Si le code du plugin inclut explicitement ces fichiers 
### il vous faudra les renommer dans les appels de include_spip !
# svn mv base/recherche_sti_upgrade.php recherche_sti_administrations.php
