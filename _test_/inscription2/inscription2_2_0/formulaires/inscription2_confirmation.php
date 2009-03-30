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

// charger cfg
include_spip('cfg_options');
// charger les fonctions de formulaires
include_spip('inc/inscription2_form_fonctions');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_inscription2_confirmation_charger_dist(){
	spip_log('charger conf','inscription2');
    $champs = array('pass1'=>'','pass2'=>'');
	
	return $champs ;
}

function formulaires_inscription2_confirmation_verifier_dist(){
  	spip_log('verif conf','inscription2');
  
	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');
	
	//initialise le tableau des erreurs
	$erreurs = array();
	
	if(_request('pass') == '') $erreurs['message_erreur'] = _T('info_passes_identiques')."<br />" ;
	
    //initilise le tableau de valeurs $champs => $valeur
    $valeurs = array();	
    $valeurs['pass'] = _request('pass');
	
	//$erreurs = $valeurs;
	//messages d'erreur au cas par cas (PASSWORD)
	//vérification des champs

	// Sinon on le verifie
		
		if($p = _request('pass')) {
			if(strlen($p)){
				if (strlen($p) < 6) {
					$erreurs['pass'] = _T('info_passe_trop_court');
					$erreurs['message_erreur'] .= _T('info_passe_trop_court')."<br />";
				}
			}
		}

	
	
		
	//message d'erreur generalise
	if (count($erreurs)) {
		spip_log($erreurs,"inscription2");
		$erreurs['message_erreur'] .= _T('inscription2:formulaire_remplir_obligatoires');
	}
	
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_inscription2_confirmation_traiter_dist($id_auteur = NULL){
	spip_log('traiter la conf','inscription2');
	global $tables_principales;
	
	$table = "spip_auteurs";
    
	//Vérification du password
		$new_pass = _request('pass');
		if (strlen($new_pass)) {
			include_spip('inc/acces');
			$htpass = generer_htpass($new_pass);
			$alea_actuel = creer_uniqid();
			$alea_futur = creer_uniqid();
			$pass = $new_pass;
			$val['pass'] = $pass;
			$val['htpass'] = $htpass;
			$val['alea_actuel'] = '';
			$val['alea_futur'] = '' ;
			$val['low_sec'] = '';
		}
		
	
	if($id_auteur = _request('id')){
	//inserer les données dans spip_auteurs -- si $id_auteur : mise à jour - autrement : nouvelle entrée
	$where = 'id_auteur = '.$id_auteur;
	sql_updateq(
	$table,
	$val,
	$where
	);
	}
	
	$message = "<p><strong>"._T('pass_nouveau_enregistre')."</strong></p><a href='?page=connexion'>S'identifier</a>";
	
    return array('editable'=>$editable,'message' => $message);
}

?>