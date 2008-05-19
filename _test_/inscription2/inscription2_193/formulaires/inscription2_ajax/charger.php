<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_inscription2_ajax_charger_dist(){

    //on obtient l'id de la personne connectée
    $id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
    
    //si on a bien un auteur alors on préremplit le formulaire avec ses informations
    if ($id_auteur) {
        $auteur = sql_fetsel(
            '*',
            'spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)',
            'id_auteur ='.$id_auteur            
        );

	    $valeurs = $auteur;
    }

    
	return $valeurs;
}
?>
