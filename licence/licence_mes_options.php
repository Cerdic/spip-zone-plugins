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

include_spip('base/licence_install');
include_spip('balise/LICENCE');


$licence_licences = array (
			"1" 	=> array(
				# nom de la licence
				"name" 	=> "Copyright",
				# numero d'identifiacation de la licence
				"id"		=> "1",
				# nom de l'icone de la licence (optionnel)
				# l'icone devra être placé dans le répertoire img_pack du plugin
				"icon"		=> "copyright-24.png",
				# Lien documentaire vers la licence (optionnel)
				"link"		=> "",
				# Description un peu plus détaillée de la licence
				"description" 	=> "© copyright auteur de l'article"),
			"2" 			=> array(
				"name" 	=> "Gnu GPL",
				"id"		=> "2",
				"icon"		=> "gnu-gpl.png",
				"link"		=> "http://www.gnu.org/copyleft/gpl.html",
				"description" => "licence GPL"),
			"3" 			=> array(
				"name" 	=> "CC by",
				"id"		=> "3",
				"icon"		=> "cc-by.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité"),
			"4" 		=> array(
				"name" 	=> "CC by-nd",
				"id"		=> "4",
				"icon"		=> "cc-by-nd.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité pas de modification"),
			"5" 	=> array(
				"name" 	=> "CC by-nc-nd",
				"id"		=> "5",
				"icon"		=> "cc-by-nc-nd.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale Pas de Modification"),	
			"6" 		=> array(
				"name" 	=> "CC by-nc",
				"id"		=> "6",
				"icon"		=> "cc-by-nc.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale"),
			"7" 	=> array(
				"name" 	=> "CC by-nc-sa",
				"id"		=> "7",
				"icon"		=> "cc-by-nc-sa.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale Partage des Conditions Initiales à l'Identique"),
			"8" 		=> array(
				"name" 	=> "CC by-sa",
				"id"		=> "8",
				"icon"		=> "cc-by-sa.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Partage des Conditions Initiales à l'Identique"));



?>