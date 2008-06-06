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
function formulaires_inscription2_ajax_charger_dist($id_auteur = NULL){

    //on obtient l'id de la personne connectée
    //$id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
    
    $valeurs = array();

    //si on a bien un auteur alors on préremplit le formulaire avec ses informations
    //les nom des champs sont les memes que ceux de la base de données
    if ($id_auteur) {
        $auteur = sql_fetsel(
            'nom,email,login'
            .',prenom,nom_famille,sexe,societe,secteur,fonction,adresse_pro,code_postal_pro,ville_pro,pays_pro,telephone_pro,fax_pro,mobile_pro',
            'spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)',
            'id_auteur ='.$id_auteur            
        );

	    $valeurs = $auteur;
    }

    
	return $valeurs;
}


function formulaires_inscription2_ajax_verifier_dist($id_auteur = NULL){
    
    //charger cfg
    include_spip('cfg_options');   

    //charge la fonction de controle du login et mail
    //$test_inscription = charger_fonction('test_inscription');

    //initialise le tableau des erreurs
    $erreurs = array();

    //messages d'erreur au cas par cas
    //vérifier les champs obligatoire
    foreach (lire_config('inscription2/') as $clef => $valeur) {
        //decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
        //?: permet de rechercher la chaine sans etre retournée dans les résultats        
        preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);        
        //si clef obligatoire, obligatoire activé et _request() vide alors erreur
        if ($resultat[1] && $valeur == 'on' && !_request($resultat[1])) {
            $erreurs[$resultat[1]] = 'Ce champ est obligatoire';   
        }
    
    }
    
    //vérifier que l'auteur a bien des droits d'édition
    if ($id_auteur) {
        include_spip('inc/autoriser');
        spip_log("autoriser : ".$id_auteur,'inscription2');
        if (!autoriser('modifier','auteur',$id_auteur)) {
            $erreurs['message_erreur'] .= "Desolé vous n'avez pas le droit de modifier cet auteur<br/>";
        }
    }    
    //vérifier certains champs spécifiquement
   
    //message d'erreur genéralisé
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= 'Veuillez remplir les champs obligatoires';
    }
    

    
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_ajax_traiter_dist($id_auteur = NULL){

	global $tables_principales;
    
    //charge les valeurs de chaque champs proposés dans le formulaire
    foreach (lire_config('inscription2/') as $clef => $valeur) {
        /* Il faut retrouver les noms des champ, 
         * par défaut inscription2 propose pour chaque champ le cas champ_obligatoire
         *  On retrouve donc les chaines de type champ_obligatoire
         *  Ensuite on verifie que le champ est proposé dans le formulaire
         *  Remplissage de $valeurs[]
         */
        //decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
        //?: permet de rechercher la chaine sans etre retournée dans les résultats
        preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);
            
        if ((!empty($resultat[1])) && (lire_config('inscription2/'.$resultat[1]) == 'on')) {
            $valeurs[$resultat[1]] = _request($resultat[1]);
        }
    }
    
    //$valeurs contient donc tous les champs remplit ou non 
    
    //definir les champs pour spip_auteurs
    $table = "spip_auteurs";
    
    //genere le tableau des valeurs à mettre à jour pour spip_auteurs
    //toutes les clefs qu'inscription2 peut mettre à jour
    $clefs = array_fill_keys(array('login','nom','email','bio'),'');
    //extrait uniquement les données qui ont été proposées à la modification
    $val = array_intersect_key($valeurs,$clefs);
                
    //inserer les données dans spip_auteurs -- si $id_auteur mise à jour autrement nouvelle entrée
    if ($id_auteur) {
        $where = 'id_auteur = '.$id_auteur;
        sql_updateq(
            $table,
            $val,
            $where
        );
    } else {
        $id_auteur = sql_insertq(
            $table,
            $val
        );
    }
    $table = 'spip_auteurs_elargis';

    //extrait les valeurs propres à spip_auteurs_elargis

    //genere le tableau des valeurs à mettre à jour pour spip_auteurs
    //toutes les clefs qu'inscription2 peut mettre à jour
    //s'appuie sur les tables definies par le plugin
    $clefs = $tables_principales['spip_auteurs_elargis']['field'];

    //extrait uniquement les données qui ont été proposées à la modification
    $val = array_intersect_key($valeurs,$clefs);

    //recherche la presence d'un complément sur l'auteur
    $id_elargi = sql_getfetsel('id','spip_auteurs_elargis','id_auteur='.$id_auteur);

    if ($id_elargi) {
        $where = 'id_auteur = '.$id_auteur;
        sql_updateq(
            $table,
            $val,
            $where      
        );
    } else {
        $id_auteur = sql_insertq(
            $table,
            $val
        );
    }
    
    if ($id_elargi) {    
        $message = "Les modifications de votre profil ont bien été prises en compte";
    } else {
        $message = "Votre inscription a bien été pris en compte. Vous allez recevoir par courrier electronique vos identifiants de connexion.";
    }

    return $message;

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
