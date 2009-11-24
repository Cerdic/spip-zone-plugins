<?php

#   +----------------------------------+
#    Nom du Filtre : licence   
#   +----------------------------------+
#    date : 11/04/2007
#    auteur :  fanouch - lesguppies@free.fr
#    version: 0.1
#    licence: GPL
#   +-------------------------------------+
#    Fonctions de ce filtre :
#	permet de lier une licence à un article 
#   +-------------------------------------+
# Pour toute suggestion, remarque, proposition d ajout
# reportez-vous au forum de l article :
# http://www.spip-contrib.net/fr_article2147.html
#   +-------------------------------------+

	spip_query("UPDATE `spip_articles` SET id_licence='".$_POST["id_licence"]."' WHERE id_article='".$_POST["id_article"]."'");
	header ("Location: ".$_SERVER["HTTP_REFERER"]."\n");

?>