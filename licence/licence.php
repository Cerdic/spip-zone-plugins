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


function licence_affiche_milieu($flux) {

	if ($flux['args']['exec'] == 'articles'){
		include_spip('inc/licence');
		$flux['data'] .= licence_formulaire_article($flux['args']['id_article'],$flux['args']['id_licence']);
	}
	return $flux;
}


?>
