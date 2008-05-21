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


function formulaires_inscription2_ajax_verifier_dist(){
    
    //charger cfg
    include_spip('cfg_options');   

    //charge la fonction de controle du login et mail
    //$test_inscription = charger_fonction('test_inscription');

    $erreurs = array();
    //message d'erreur au cas par cas
    if (!_request('nom')) $erreurs['nom'] = 'Ce champ est obligatoire';
    if (!_request('email')) $erreurs['email'] = 'Veuillez saisir votre email de la forme \'nom@domaine.com\'';
    if (!_request('username')) $erreurs['username'] = 'Votre identifiant est obligatoire';
    
    //message d'erreur genéralisé
    if (count($erreurs)) $erreurs['message_erreur'] = 'Veuillez remplir les champs obligatoires';
    
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

/*

// http://doc.spip.org/@test_inscription_dist
function test_inscription_dist($mode, $mail, $nom, $id=0) {

    include_spip('inc/filtres');
    $nom = trim(corriger_caracteres($nom));
    if (!$nom || strlen($nom) > 64)
        return _T('ecrire:info_login_trop_court');
    if (!$r = email_valide($mail)) return _T('info_email_invalide');
    return array('email' => $r, 'nom' => $nom, 'bio' => $mode);
}
*/

?>
